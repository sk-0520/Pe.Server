<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;


class ErrorHandler
{
	private bool $isRegistered = false;

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
		set_error_handler([$this, 'receiveError']); //@phpstan-ignore-line なんやねんもう！
		$this->isRegistered = true;
	}

	public final function receiveShutdown(): void
	{
		$lastError = error_get_last();
		if (is_null($lastError)) {
			return;
		}

		/** @var int */
		$type = ArrayUtility::getOr($lastError, 'type', -1);
		/** @var string */
		$message = ArrayUtility::getOr($lastError, 'message', '');
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

	public final function receiveException(Throwable $throwable): void
	{
		$this->_catchError(
			Throws::getErrorCode($throwable),
			$throwable->getMessage(),
			$throwable->getFile(),
			$throwable->getLine(),
			$throwable
		);
	}

	public final function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber/* , array $_ */): void
	{
		$this->_catchError(
			$errorNumber,
			$errorMessage,
			$errorFile,
			$errorLineNumber,
			null
		);
	}

	protected final function setHttpStatus(?Throwable $throwable): HttpStatus
	{
		$status = $throwable instanceof HttpStatusException
			? $throwable->status
			: HttpStatus::serviceUnavailable();

		http_response_code($status->getCode());

		return $status;
	}

	private function _catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$this->catchError($errorNumber, $message, $file, $lineNumber, $throwable);
		exit;
	}

	/**
	 * エラー取得処理。
	 *
	 * @param integer $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param integer $lineNumber
	 * @param Throwable|null $throwable
	 * @return void
	 */
	public function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$values = [
			'error_number' => $errorNumber,
			'message' => $message,
			'file' => $file,
			'line_number' => $lineNumber,
			'throwable' => $throwable,
		];

		$logger = Logging::create(__CLASS__);
		$logger->error($values);

		$status = $this->setHttpStatus($throwable);

		$template = Template::create('template', 'Core');
		echo $template->build('error-display.tpl', new TemplateParameter($status, $values, []));
	}
}
