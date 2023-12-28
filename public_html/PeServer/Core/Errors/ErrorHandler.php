<?php

declare(strict_types=1);

namespace PeServer\Core\Errors;

use PeServer\Core\Code;
use PeServer\Core\Collection\Arr;
use PeServer\Core\DI\Inject;
use PeServer\Core\DisposerBase;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\ResponsePrinter;
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
use PeServer\Core\ResultData;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidErrorLevelError;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;
use Throwable;

/**
 * エラーハンドリング処理。
 */
class ErrorHandler
{
	#region variable

	/** 登録済みか。 */
	private bool $isRegistered = false;

	#endregion

	public function __construct(
		protected readonly ILogger $logger
	) {
	}

	#region function

	/**
	 * エラーハンドラの登録処理。
	 *
	 * 明示的に呼び出しが必要。
	 *
	 * @return void
	 */
	final public function register(): void
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
	final public function receiveShutdown(): void
	{
		$lastError = error_get_last();
		if ($lastError === null) {
			return;
		}

		$type = Arr::getOr($lastError, 'type', -1);
		$message = Arr::getOr($lastError, 'message', Text::EMPTY);
		$file = Arr::getOr($lastError, 'file', '<unknown>');
		$line = Arr::getOr($lastError, 'line', 0);

		$this->catchErrorCore(
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
	 */
	final public function receiveException(Throwable $throwable): never
	{
		$this->catchErrorCore(
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
	 * @param int $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLineNumber
	 */
	final public function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber/* , array $_ */): never
	{
		$this->catchErrorCore(
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
	public static function trap(callable $action, int $errorLevel = E_ALL): ResultData
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
	 * エラー取得処理（呼び出し側）。
	 *
	 * こいつが呼ばれた時点でもはや何もできない。
	 *
	 * @param int $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param int $lineNumber
	 * @param Throwable|null $throwable
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	private function catchErrorCore(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): never
	{
		$this->catchError($errorNumber, $message, $file, $lineNumber, $throwable);
		exit($errorNumber);
	}

	/**
	 * エラー取得処理（本体）。
	 *
	 * @param int $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param int $lineNumber
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

		var_dump($values);
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
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
	 * @param int $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLineNumber
	 * @return bool
	 */
	final public function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber): bool
	{
		$this->isError = true;
		return $this->isError;
	}
}
