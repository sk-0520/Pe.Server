<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty_Internal_Template;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;


abstract class TemplateFunctionBase extends TemplatePluginBase implements ITemplateFunction
{
	/**
	 * Undocumented variable
	 *
	 * @var array<string,string>
	 */
	protected array $params;

	protected Smarty_Internal_Template $smarty;

	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	protected abstract function functionBodyImpl(): string;

	public function functionBody(array $params, Smarty_Internal_Template $smarty): string
	{
		$this->params = $params;
		$this->smarty = $smarty;

		return $this->functionBodyImpl();
	}
}
