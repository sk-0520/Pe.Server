<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \Throwable;
use PeServer\Core\Environment;
use PeServer\Core\Log\Logging;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\ErrorHandler;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AppTemplate;

final class AppErrorHandler extends ErrorHandler
{
	private RequestPath $requestPath;

	public function __construct(RequestPath $requestPath)
	{
		$this->requestPath = $requestPath;
	}

	public function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$next = true;

		$isProduction = Environment::isProduction();

		if ($isProduction) {
			$next = false;
		}

		$isJson = StringUtility::startsWith($this->requestPath->full, 'api/', true) || StringUtility::startsWith($this->requestPath->full, 'ajax/', true);
		if ($isJson) {
			$next = false;
		}

		if (!$next) {
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

			if ($isJson) {
				header('Content-Type: application/json');
				if ($isProduction) {
					unset($values['throwable']);
					echo json_encode($values);
				} else {
					echo json_encode($values);
				}
			} else {
				echo AppTemplate::createPageTemplate('error', $values, $status);
			}

			return;
		}

		parent::catchError($errorNumber, $message, $file, $lineNumber, $throwable);
	}
}
