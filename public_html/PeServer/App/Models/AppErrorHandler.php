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

	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
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

			$status = $this->setHttpStatus($throwable);

			$logger = Logging::create(get_class($this));

			$isSuppressionStatus = false;
			foreach ($this->getSuppressionStatusList() as $suppressionStatus) {
				if ($status->is($suppressionStatus)) {
					$isSuppressionStatus = true;
					$logger->info('HTTP: ' . $suppressionStatus->getCode());
					break;
				}
			}
			if (!$isSuppressionStatus) {
				$logger->error($values);
			}

			if ($isJson) {
				unset($values['error_number']);
				unset($values['message']);
				if ($isProduction) {
					unset($values['throwable']);
				}

				$response = ResponseJson::error(
					$message,
					strval($errorNumber),
					$values
				);

				$json = [
					'data' => $response->data,
					'error' => $response->error,
				];

				header('Content-Type: application/json');
				echo json_encode($json);
			} else {
				echo AppTemplate::buildPageTemplate('error', $values, $status);
			}

			return;
		}

		parent::catchError($errorNumber, $message, $file, $lineNumber, $throwable);
	}
}
