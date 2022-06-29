<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Mvc\Template;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\IActionResult;
use PeServer\Core\Mvc\TemplateParameter;

class ViewActionResult implements IActionResult
{
	/**
	 * 生成。
	 *
	 * @param string $templateBaseName
	 * @param string $actionName
	 * @param TemplateParameter $templateParameter
	 * @param array<string,string[]> $headers
	 */
	public function __construct(
		/** @readonly */
		private string $templateBaseName,
		/** @readonly */
		private string $actionName,
		/** @readonly */
		private TemplateParameter $templateParameter,
		/** @readonly */
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
			$response->header->addValue('Content-Type', 'text/html');
		}


		$template = Template::create($this->templateBaseName);
		$response->content = $template->build("$this->actionName.tpl", $this->templateParameter);

		return $response;
	}
}
