<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;
use PeServer\Core\DefaultValue;
use PeServer\Core\Throws\TypeException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;

abstract class TemplateBlockFunctionBase extends TemplateFunctionBase implements ITemplateBlockFunction
{
	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region ITemplateBlockFunction

	protected abstract function functionBlockBodyImpl(string $content): string;

	public function functionBlockBody(array $params, mixed $content, Smarty_Internal_Template $template, bool &$repeat): string
	{
		if ($repeat) {
			$this->params = $params;
			$this->template = $template;

			return DefaultValue::EMPTY_STRING;
		}

		if (!is_string($content)) {
			throw new TypeException();
		}

		return $this->functionBlockBodyImpl((string)$content);
	}

	#endregion

	#region TemplateFunctionBase

	protected final function functionBodyImpl(): string
	{
		throw new NotSupportedException();
	}

	public final function functionBody(array $params, Smarty_Internal_Template $smarty): string
	{
		throw new NotSupportedException();
	}

	#endregion
}
