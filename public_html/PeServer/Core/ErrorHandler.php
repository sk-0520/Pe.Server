<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;


abstract class ErrorHandler implements IErrorHandler
{
	private static IErrorHandler|null $core;
	public static function core(): IErrorHandler
	{
		return self::$core ??= new _CoreErrorHandler();
	}

	private static array $handlers = [];

	public static function register(IErrorHandler $handler)
	{
		self::$handlers[] = $handler;
	}

	public function __construct()
	{
		register_shutdown_function([$this, 'receiveShutdown']);
		set_exception_handler([$this, 'receiveException']);
		set_error_handler([$this, 'receiveError']);
	}

	public function receiveShutdown()
	{
		$lastError = error_get_last();
		if (is_null($lastError)) {
			return;
		}

		$this->catchError(
			ArrayUtility::getOr($lastError, 'type', -1),
			ArrayUtility::getOr($lastError, 'message', ''),
			ArrayUtility::getOr($lastError, 'file', '<unknown>'),
			ArrayUtility::getOr($lastError, 'line', 0),
			null
		);
		exit;
	}

	public function receiveException(Throwable $throwable)
	{
		$this->catchError(
			Throws::getErrorCode($throwable),
			$throwable->getMessage(),
			$throwable->getFile(),
			$throwable->getLine(),
			$throwable
		);
		exit;
	}

	public function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber/* , array $_ */)
	{
		$this->catchError(
			$errorNumber,
			$errorMessage,
			$errorFile,
			$errorLineNumber,
			null
		);
		exit;
	}

	protected function setHttpStatus(?Throwable $throwable)
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
	protected function applyTemplate(string $templateName, string $baseName, string $templateBaseName, array $values)
	{
		$template = Template::create($baseName, $templateBaseName);
		$template->show($templateName, new TemplateParameter(HttpStatus::ok(), $values, []));
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

		$this->setHttpStatus($throwable);
		$this->applyTemplate('error-display.tpl', 'template', 'Core', $values);
	}
}
