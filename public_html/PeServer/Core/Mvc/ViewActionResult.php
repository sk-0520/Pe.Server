<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\HttpStatus;

class ViewActionResult extends ActionResult
{
	private string $templateBaseName;
	private string $actionName;
	private TemplateParameter $templateParameter;

	/**
	 * Undocumented function
	 *
	 * @param string $templateBaseName
	 * @param string $actionName
	 * @param TemplateParameter $templateParameter
	 * @param array<string,string[]> $headers
	 */
	public function __construct(string $templateBaseName, string $actionName, TemplateParameter $templateParameter, array $headers)
	{
		parent::__construct($headers);

		$this->templateBaseName = $templateBaseName;
		$this->actionName = $actionName;
		$this->templateParameter = $templateParameter;
	}

	protected function body(): void
	{
		$template = Template::create($this->templateBaseName);
		$template->show("$this->actionName.tpl", $this->templateParameter);
	}
}
