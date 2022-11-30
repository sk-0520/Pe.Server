<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Throws\InvalidOperationException;

abstract class TemplateModifierBase extends TemplatePluginBase implements ITemplateModifier
{
	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	protected abstract function modifierBodyImpl(mixed $value, array $params, Smarty_Internal_Template $smarty): string;

	#region ITemplateModifier

	public abstract function getModifierName(): string;

	public function modifierBody(mixed $value, array $params, Smarty_Internal_Template $smarty): string
	{
		return $this->modifierBodyImpl($value, $params, $smarty);
	}

	#endregion
}
