<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Mime;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\Result\IActionResult;

/**
 * 捜査結果: View。
 *
 * @immutable
 */
class ViewActionResult implements IActionResult
{
	/**
	 * 生成。
	 *
	 * @param string $templateBaseName
	 * @param string $actionName
	 * @param TemplateParameter $templateParameter
	 * @param array<string,string[]> $headers
	 * @phpstan-param array<non-empty-string,string[]> $headers
	 */
	public function __construct(
		private string $templateBaseName,
		private string $actionName,
		private TemplateParameter $templateParameter,
		private array $headers
	) {
	}

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = $this->templateParameter->httpStatus;

		foreach ($this->headers as $name => $headers) {
			$response->header->setValues($name, $headers);
		}
		if (!$response->header->existsHeader('Content-Type')) {
			$response->header->addValue('Content-Type', Mime::HTML);
		}


		$template = Template::create($this->templateBaseName, InitialValue::EMPTY_STRING, InitialValue::EMPTY_STRING);
		$response->content = $template->build("$this->actionName.tpl", $this->templateParameter);

		return $response;
	}
}
