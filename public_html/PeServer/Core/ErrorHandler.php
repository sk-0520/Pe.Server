<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use PeServer\Core\Log\Logging;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidErrorLevelError;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * エラーハンドリング処理。
 */
class ErrorHandler
{
	/** 登録済みか。 */
	private bool $isRegistered = false;

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
		if (is_null($lastError)) {
			return;
		}

		/** @var int */
		$type = ArrayUtility::getOr($lastError, 'type', -1);
		/** @var string */
		$message = ArrayUtility::getOr($lastError, 'message', InitialValue::EMPTY_STRING);
		/** @var string */
		$file = ArrayUtility::getOr($lastError, 'file', '<unknown>');
		/** @var int */
		$line = ArrayUtility::getOr($lastError, 'line', 0);

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
	 * @phpstan-param callable(): (TValue) $action 補足したい処理。
	 * @param int $errorLevel 補足対象のエラーレベル。 https://www.php.net/manual/ja/errorfunc.constants.php
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
		exit;
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
		];

		$status = $this->setHttpStatus($throwable);

		$logger = Logging::create(get_class($this));

		$isSuppressionStatus = false;
		foreach ($this->getSuppressionStatusList() as $suppressionStatus) {
			if ($status->is($suppressionStatus)) {
				$isSuppressionStatus = true;
				$logger->info('HTTP: {0}', $suppressionStatus->getCode());
				break;
			}
		}
		if (!$isSuppressionStatus) {
			$logger->error($values);
		}

		$template = Template::create('template', 'Core');
		echo $template->build('error-display.tpl', new TemplateParameter($status, $values, []));
	}
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
		parent::disposeImpl();

		restore_error_handler();
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
