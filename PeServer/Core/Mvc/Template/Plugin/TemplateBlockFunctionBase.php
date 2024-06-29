<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use TypeError;
use Smarty\Template;
use PeServer\Core\Text;
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

	public function functionBlockBody(array $params, mixed $content, Template $template, bool &$repeat): string
	{
		if ($repeat) {
			$this->params = $params;
			$this->smartyTemplate = $template;

			return Text::EMPTY;
		}

		if (!is_string($content)) {
			throw new TypeError();
		}

		return $this->functionBlockBodyImpl((string)$content);
	}

	#endregion

	#region TemplateFunctionBase

	final protected function functionBodyImpl(): string
	{
		throw new NotSupportedException();
	}

	final public function functionBody(array $params, Template $smarty): string
	{
		throw new NotSupportedException();
	}

	#endregion
}
