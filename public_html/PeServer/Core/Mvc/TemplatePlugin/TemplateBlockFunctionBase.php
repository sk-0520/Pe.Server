<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty_Internal_Template;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\Throws\NotSupportedException;

abstract class TemplateBlockFunctionBase extends TemplateFunctionBase implements ITemplateBlockFunction
{
	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	protected abstract function functionBlockBodyImpl(string $content): string;

	public function functionBlockBody(array $params, mixed $content, Smarty_Internal_Template $template, bool &$repeat): string
	{
		if ($repeat) {
			$this->params = $params;
			$this->template = $template;

			return '';
		}

		return $this->functionBlockBodyImpl($content);
	}

	protected final function functionBodyImpl(): string
	{
		throw new NotSupportedException();
	}

	public final function functionBody(array $params, Smarty_Internal_Template $smarty): string
	{
		throw new NotSupportedException();
	}
}
