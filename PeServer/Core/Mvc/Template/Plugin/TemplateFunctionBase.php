<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty\Template;
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

	protected Template $smartyTemplate;

	#endregion

	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	abstract protected function functionBodyImpl(): string;

	#endregion

	#region ITemplateBlockFunction

	public function functionBody(array $params, Template $smartyTemplate): string
	{
		$this->params = $params;
		$this->smartyTemplate = $smartyTemplate;

		return $this->functionBodyImpl();
	}

	#endregion

	#region TemplatePluginBase

	protected function existsError(): bool
	{
		return $this->existsSmartyError($this->smartyTemplate);
	}
	/**
	 * Undocumented function
	 *
	 * @return array<string,string[]>
	 */
	protected function getErrors(): array
	{
		return $this->getSmartyErrors($this->smartyTemplate);
	}

	protected function existsValues(): bool
	{
		return $this->existsSmartyValues($this->smartyTemplate);
	}
	/**
	 * Undocumented function
	 *
	 * @return array<string,string|string[]|bool|int|object>
	 */
	protected function getValues(): array
	{
		return $this->getSmartyValues($this->smartyTemplate);
	}

	#endregion
}
