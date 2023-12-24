<?php

declare(strict_types=1);

namespace PeServer\Core\Errors;

use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;
use Throwable;

class HttpErrorHandler extends ErrorHandler
{
	#region variable

	#[Inject(TemplateFactory::class)] //@phpstan-ignore-next-line [INJECT]
	private ITemplateFactory $templateFactory;

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private IResponsePrinterFactory $responsePrinterFactory;

	#[Inject] //@phpstan-ignore-next-line [INJECT]
	private WebSecurity $webSecurity;

	#endregion

	#region function

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
			"$file" => File::readContent($file)->raw,
		];

		if ($throwable !== null) {
			foreach ($throwable->getTrace() as $item) {
				if (isset($item['file'])) {
					$f = $item['file'];
					if (!isset($files[$f])) {
						$files[$f] = File::readContent($f)->raw;
					}
				}
			}
		}

		return $files;
	}

	/**
	 * 抑制エラーコード指定。
	 *
	 * @return HttpStatus[]
	 */
	protected function getSuppressionStatusList(): array
	{
		return [
			HttpStatus::NotFound,
		];
	}


	/**
	 * 例外からHTTP応答ステータスコードを取得する。
	 *
	 * @param Throwable|null $throwable
	 * @return HttpStatus 設定されたHTTPステータスコード。
	 */
	final protected function getHttpStatus(?Throwable $throwable): HttpStatus
	{
		$status = $throwable instanceof HttpStatusException
			? $throwable->status
			: HttpStatus::ServiceUnavailable;

		return $status;
	}

	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$response = new HttpResponse();
		$response->status = $this->getHttpStatus($throwable);

		$values = [
			'error_number' => $errorNumber,
			'message' => $message,
			'file' => $file,
			'line_number' => $lineNumber,
			'throwable' => $throwable,
			'cache' => $this->getFileContents($file, $throwable)
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

		$options = new TemplateOptions(
			Path::combine(__DIR__, '..'),
			'template',
			UrlHelper::none(),
			$this->webSecurity,
			Path::combine(Directory::getTemporaryDirectory(), 'PeServer-Core')
		);
		$template = $this->templateFactory->createTemplate($options);

		$response->content = $template->build('error-display.tpl', new TemplateParameter($response->status, $values, []));

		$printer = $this->responsePrinterFactory->createResponsePrinter(HttpRequest::none(), $response);

		$printer->execute();
	}

	#endregion
}
