<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Log\Logging;
use \Throwable;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;


abstract class ErrorHandler
{
	private static ErrorHandler|null $core;
	public static function core(): ErrorHandler
	{
		return self::$core ??= new _CoreErrorHandler();
	}

	public final function register(): void
	{
		register_shutdown_function([$this, 'receiveShutdown']);
		set_exception_handler([$this, 'receiveException']);
		set_error_handler([$this, 'receiveError']); //@phpstan-ignore-line なんやねんもう！
	}

	public final function receiveShutdown(): void
	{
		$lastError = error_get_last();
		if (is_null($lastError)) {
			return;
		}

		$this->_catchError(
			ArrayUtility::getOr($lastError, 'type', -1),
			ArrayUtility::getOr($lastError, 'message', ''),
			ArrayUtility::getOr($lastError, 'file', '<unknown>'),
			ArrayUtility::getOr($lastError, 'line', 0),
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

	protected final function setHttpStatus(?Throwable $throwable): void
	{
		if ($throwable instanceof HttpStatusException) {
			http_response_code($throwable->getCode());
		} else {
			http_response_code(HttpStatus::serviceUnavailable()->code());
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $baseName
	 * @param string $templateBaseName
	 * @param string $templateName
	 * @param array<string,mixed> $values
	 * @return no-return
	 */
	protected function applyTemplate(string $templateName, string $baseName, string $templateBaseName, array $values): void
	{
		$template = Template::create($baseName, $templateBaseName);
		$template->show($templateName, new TemplateParameter(HttpStatus::ok(), $values, []));
	}

	private function _catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$this->catchError($errorNumber, $message, $file, $lineNumber, $throwable);
		exit;
	}

	public abstract function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void;
}

final class _CoreErrorHandler extends ErrorHandler
{
	public function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$values = [
			'error_number' => $errorNumber,
			'message' => $message,
			'file' => $file,
			'line_number' => $lineNumber,
			'throwable' => $throwable,
		];

		$logger = Logging::create('error');
		$logger->error($values);

		$this->setHttpStatus($throwable);
		$this->applyTemplate('error-display.tpl', 'template', 'Core', $values);
	}
}
