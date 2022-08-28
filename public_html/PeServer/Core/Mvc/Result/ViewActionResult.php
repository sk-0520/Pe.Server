<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Result;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Web\IUrlHelper;

/**
 * 結果操作: View。
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
		protected string $templateBaseName,
		protected string $actionName,
		protected TemplateParameter $templateParameter,
		protected array $headers,
		protected ITemplateFactory $templateFactory,
		protected IUrlHelper $urlHelper
	) {
	}

	#region IActionResult

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

		// $options = new AppTemplateOptions(
		// 	$this->templateBaseName,
		// 	$this->urlHelper
		// );
		$options = new TemplateOptions(
			__DIR__ . '/../../template',
			'',
			$this->urlHelper,
			Path::combine(Directory::getTemporaryDirectory(), 'PeServer-Core', 'template')
		);
		$template = $this->templateFactory->createTemplate($options);

		$response->content = $template->build($this->actionName . '.tpl', $this->templateParameter);

		return $response;
	}

	#endregion
}
