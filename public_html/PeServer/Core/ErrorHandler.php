<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\SmartyTemplate;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidErrorLevelError;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Web\UrlHelper;

/**
 * エラーハンドリング処理。
 */
class ErrorHandler
{
	#region variable

	/** 登録済みか。 */
	private bool $isRegistered = false;

	#[Inject(TemplateFactory::class)] //@phpstan-ignore-next-line
	private ITemplateFactory $templateFactory;

	#endregion

	public function __construct(
		protected ILogger $logger
	) {
	}

	#region function

	/**
	 * 抑制エラーコード指定。
	 *
	 * @return HttpStatus[]
	 */
	protected function getSuppressionStatusList(): array
	{
		return [
			HttpStatus::notFound(),
		];
	}

	/**
	 * エラーハンドラの登録処理。
	 *
	 * 明示的に呼び出しが必要。
	 *
	 * @return void
	 */
	public final function register(): void
	{
		if ($this->isRegistered) {
			throw new InvalidOperationException();
		}

		register_shutdown_function([$this, 'receiveShutdown']);
		set_exception_handler([$this, 'receiveException']);
		set_error_handler([$this, 'receiveError']);
		$this->isRegistered = true;
	}

	/**
	 * シャットダウン処理でエラーがあれば処理する。
	 */
	public final function receiveShutdown(): void
	{
		$lastError = error_get_last();
		if ($lastError === null) {
			return;
		}

		/** @var int */
		$type = Arr::getOr($lastError, 'type', -1);
		/** @var string */
		$message = Arr::getOr($lastError, 'message', Text::EMPTY);
		/** @var string */
		$file = Arr::getOr($lastError, 'file', '<unknown>');
		/** @var int */
		$line = Arr::getOr($lastError, 'line', 0);

		$this->_catchError(
			$type,
			$message,
			$file,
			$line,
			null
		);
	}

	/**
	 * 未ハンドル例外を処理する。
	 *
	 * @param Throwable $throwable
	 * @return no-return
	 */
	public final function receiveException(Throwable $throwable)
	{
		$this->_catchError(
			Throws::getErrorCode($throwable),
			$throwable->getMessage(),
			$throwable->getFile(),
			$throwable->getLine(),
			$throwable
		);
	}

	/**
	 * エラーを処理する。
	 *
	 * @param integer $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLineNumber
	 * @return no-return
	 */
	public final function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber/* , array $_ */)
	{
		$this->_catchError(
			$errorNumber,
			$errorMessage,
			$errorFile,
			$errorLineNumber,
			null
		);
	}

	/**
	 * E_ERROR 的なやつらを一時的に補足する。
	 *
	 * @template TValue
	 * @param callable $action 補足したい処理。
	 * @phpstan-param callable():TValue $action 補足したい処理。
	 * @param int $errorLevel 補足対象のエラーレベル。 https://www.php.net/manual/errorfunc.constants.php
	 * @return ResultData 結果。補足できたかどうかの真偽値が成功状態に設定されるので処理の結果自体は呼び出し側で確認すること。
	 * @phpstan-return ResultData<TValue>
	 */
	public static function trapError(callable $action, int $errorLevel = E_ALL): ResultData
	{
		return Code::using(new LocalPhpErrorReceiver($errorLevel), function (LocalPhpErrorReceiver $disposable) use ($action) {
			$result = $action();
			if ($disposable->isError) {
				/** @phpstan-var ResultData<TValue> */
				return ResultData::createFailure();
			}

			return ResultData::createSuccess($result);
		});
	}

	/**
	 * 例外からHTTP応答ステータスコードを設定する。
	 *
	 * @param Throwable|null $throwable
	 * @return HttpStatus 設定されたHTTPステータスコード。
	 */
	protected final function setHttpStatus(?Throwable $throwable): HttpStatus
	{
		$status = $throwable instanceof HttpStatusException
			? $throwable->status
			: HttpStatus::serviceUnavailable();

		http_response_code($status->getCode());

		return $status;
	}

	/**
	 * エラー取得処理（呼び出し側）。
	 *
	 * こいつが呼ばれた時点でもはや何もできない。
	 *
	 * @param integer $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param integer $lineNumber
	 * @param Throwable|null $throwable
	 * @return no-return
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	private function _catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable)
	{
		$this->catchError($errorNumber, $message, $file, $lineNumber, $throwable);
		exit($errorNumber);
	}

	/**
	 * 検出できるソースファイル内容をすべて取得。
	 *
	 * @param string $file
	 * @param Throwable|null $throwable
	 * @return array<string,string>
	 */
	private function getFileContents(string $file, ?Throwable $throwable): array
	{
		$files = [
			"$file" => File::readContent($file)->getRaw(),
		];

		if ($throwable !== null) {
			foreach ($throwable->getTrace() as $item) {
				if (isset($item['file'])) {
					$f = $item['file'];
					if (!isset($files[$f])) {
						$files[$f] = File::readContent($f)->getRaw();
					}
				}
			}
		}

		return $files;
	}

	/**
	 * エラー取得処理（本体）。
	 *
	 * @param integer $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param integer $lineNumber
	 * @param Throwable|null $throwable
	 * @return void
	 */
	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$values = [
			'error_number' => $errorNumber,
			'message' => $message,
			'file' => $file,
			'line_number' => $lineNumber,
			'throwable' => $throwable,
			'cache' => $this->getFileContents($file, $throwable)
		];

		$status = $this->setHttpStatus($throwable);

		$isSuppressionStatus = false;
		foreach ($this->getSuppressionStatusList() as $suppressionStatus) {
			if ($status->is($suppressionStatus)) {
				$isSuppressionStatus = true;
				$this->logger->info('HTTP: {0}', $suppressionStatus->getCode());
				break;
			}
		}
		if (!$isSuppressionStatus) {
			$this->logger->error($values);
		}

		$options = new TemplateOptions(
			__DIR__,
			'template',
			UrlHelper::none(),
			Path::combine(Directory::getTemporaryDirectory(), 'PeServer-Core')
		);
		$template = $this->templateFactory->createTemplate($options);

		echo $template->build('error-display.tpl', new TemplateParameter($status, $values, []));
	}

	#endregion
}

final class LocalPhpErrorReceiver extends DisposerBase
{
	public bool $isError = false;

	public function __construct(int $errorLevel)
	{
		if (!$errorLevel) {
			throw new InvalidErrorLevelError();
		}

		set_error_handler([$this, 'receiveError'], $errorLevel);
	}

	protected function disposeImpl(): void
	{
		restore_error_handler();

		parent::disposeImpl();
	}

	/**
	 * エラーを処理する。
	 *
	 * @param integer $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLineNumber
	 * @return bool
	 */
	public final function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber): bool
	{
		$this->isError = true;
		return $this->isError;
	}
}
