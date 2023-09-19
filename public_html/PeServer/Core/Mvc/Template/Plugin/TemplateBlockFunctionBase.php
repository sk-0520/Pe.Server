<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty_Internal_Template;
use PeServer\Core\Text;
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

	#region function

	abstract protected function functionBlockBodyImpl(string $content): string;

	#endregion

	#region ITemplateBlockFunction

	public function functionBlockBody(array $params, mixed $content, Smarty_Internal_Template $template, bool &$repeat): string
	{
		if ($repeat) {
			$this->params = $params;
			$this->template = $template;

			return Text::EMPTY;
		}

		if (!is_string($content)) {
			throw new TypeException();
		}

		return $this->functionBlockBodyImpl((string)$content);
	}

	#endregion

	#region TemplateFunctionBase

	final protected function functionBodyImpl(): string
	{
		throw new NotSupportedException();
	}

	final public function functionBody(array $params, Smarty_Internal_Template $smarty): string
	{
		throw new NotSupportedException();
	}

	#endregion
}
