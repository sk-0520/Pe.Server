<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Result\ViewActionResult;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Web\IUrlHelper;

readonly final class AppViewActionResult extends ViewActionResult
{
	#region ViewActionResult

	public function createResponse(): HttpResponse
	{
		$response = new HttpResponse();

		$response->status = $this->templateParameter->httpStatus;

		foreach ($this->headers as $name => $headers) {
			$response->header->setValues($name, $headers);
		}
		if (!$response->header->existsHeader(ContentType::NAME)) {
			$response->header->addValue(ContentType::NAME, Mime::HTML);
		}

		$options = new AppTemplateOptions(
			$this->templateBaseName,
			$this->urlHelper,
			$this->webSecurity
		);
		$template = $this->templateFactory->createTemplate($options);

		$response->content = $template->build($this->actionName . '.tpl', $this->templateParameter);

		return $response;
	}

	#endregion
}
