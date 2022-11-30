<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;

abstract class TemplateFunctionBase extends TemplatePluginBase implements ITemplateFunction
{
	#region variable

	/**
	 * Undocumented variable
	 *
	 * @var array<string,string>
	 */
	protected array $params;

	protected Smarty_Internal_Template $template;

	#endregion

	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	protected abstract function functionBodyImpl(): string;

	#endregion

	#region ITemplateBlockFunction

	public function functionBody(array $params, Smarty_Internal_Template $template): string
	{
		$this->params = $params;
		$this->template = $template;

		return $this->functionBodyImpl();
	}

	#endregion

	#region TemplatePluginBase

	protected function existsError(): bool
	{
		return $this->existsSmartyError($this->template);
	}
	/**
	 * Undocumented function
	 *
	 * @return array<string,string[]>
	 */
	protected function getErrors(): array
	{
		return $this->getSmartyErrors($this->template);
	}

	protected function existsValues(): bool
	{
		return $this->existsSmartyValues($this->template);
	}
	/**
	 * Undocumented function
	 *
	 * @return array<string,string|string[]|bool|int|object>
	 */
	protected function getValues(): array
	{
		return $this->getSmartyValues($this->template);
	}

	#endregion
}
