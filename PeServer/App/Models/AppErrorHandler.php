<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\DI\Inject;
use PeServer\Core\Environment;
use PeServer\Core\Errors\ErrorHandler;
use PeServer\Core\Errors\HttpErrorHandler;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use Throwable;

final class AppErrorHandler extends HttpErrorHandler
{
	#region variable

	private RequestPath $requestPath;
	private JsonSerializer $jsonSerializer;
	private ITemplateFactory $templateFactory;

	#endregion

	public function __construct(
		RequestPath $requestPath,
		TemplateFactory $templateFactory,
		private IResponsePrinterFactory $responsePrinterFactory,
		private ProgramContext $programContext,
		private IUrlHelper $urlHelper,
		private WebSecurity $webSecurity,
		?JsonSerializer $jsonSerializer,
		private Environment $environment,
		ILogger $logger
	) {
		parent::__construct($logger);

		$this->requestPath = $requestPath;
		$this->templateFactory = $templateFactory;
		$this->jsonSerializer = $jsonSerializer ?? new JsonSerializer();
	}

	#region ErrorHandler

	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$next = true;

		$isProduction = $this->environment->isProduction();

		if ($isProduction) {
			$next = false;
		}

		$isJson = Text::startsWith($this->requestPath->full, 'api/', true) || Text::startsWith($this->requestPath->full, 'ajax/', true);
		if ($isJson) {
			$next = false;
		}

		// デバッグ環境で本番用エラー検証用
		//$next = false;

		if (!$next) {
			$response = new HttpResponse();
			$response->status = $this->getHttpStatus($throwable);

			$values = [
				'error_number' => $errorNumber,
				'message' => $message,
				'file' => $file,
				'line_number' => $lineNumber,
				'throwable' => $throwable,
			];

			$isSuppressionStatus = false;
			foreach ($this->getSuppressionStatusList() as $suppressionStatus) {
				if ($response->status === $suppressionStatus) {
					$isSuppressionStatus = true;
					$this->logger->info('HTTP {0}: {1}', $suppressionStatus->value, $suppressionStatus->name);
					break;
				}
			}
			if (!$isSuppressionStatus) {
				$this->logger->error($values);
			}

			if ($isJson) {
				unset($values['error_number']);
				unset($values['message']);
				if ($isProduction) {
					unset($values['throwable']);
				}

				$responseJson = ResponseJson::error(
					$message,
					strval($errorNumber),
					$values
				);

				$json = [
					'data' => $responseJson->data,
					'error' => $responseJson->error,
				];

				$response->header->addValue(ContentType::NAME, Mime::JSON);
				$response->content = $this->jsonSerializer->save($json);
			} else {
				$rootDir = Path::combine($this->programContext->applicationDirectory, 'App', 'Views');
				$baseDir = Path::combine('template', 'page');

				$options = new TemplateOptions(
					$rootDir,
					$baseDir,
					$this->programContext,
					$this->urlHelper,
					$this->webSecurity,
					Path::combine(Directory::getTemporaryDirectory(), 'PeServer-App')
				);
				$template = $this->templateFactory->createTemplate($options);
				$response->content =  $template->build('error.tpl', new TemplateParameter($response->status, $values, []));
			}

			$printer = $this->responsePrinterFactory->createResponsePrinter(HttpRequest::none(), $response);
			$printer->execute();
			return;
		}

		parent::catchError($errorNumber, $message, $file, $lineNumber, $throwable);
	}

	#endregion
}
