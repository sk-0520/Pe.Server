<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \Throwable;
use PeServer\App\Models\AppTemplate;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Serialization\Json;
use PeServer\Core\Log\Logging;
use PeServer\Core\Text;

final class AppErrorHandler extends ErrorHandler
{
	private RequestPath $requestPath;
	private Json $json;

	public function __construct(RequestPath $requestPath, ?Json $json = null)
	{
		$this->requestPath = $requestPath;
		$json ??= new Json();
		$this->json = $json;
	}

	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$next = true;

		$isProduction = Environment::isProduction();

		if ($isProduction) {
			$next = false;
		}

		$isJson = Text::startsWith($this->requestPath->full, 'api/', true) || Text::startsWith($this->requestPath->full, 'ajax/', true);
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
					$logger->info('HTTP: {0}', $suppressionStatus->getCode());
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
				echo $this->json->encode($json);
			} else {
				echo AppTemplate::buildPageTemplate('error', $values, $status);
			}

			return;
		}

		parent::catchError($errorNumber, $message, $file, $lineNumber, $throwable);
	}
}
