<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Smarty_Internal_Template;
use PeServer\Core\Mvc\Template\Plugin\TemplateModifierBase;
use PeServer\Core\Mvc\Template\Plugin\ITemplateModifier;

class VarDumpModifier extends TemplateModifierBase implements ITemplateModifier
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region ITemplateModifier

	public function getModifierName(): string
	{
		return 'var_dump';
	}

	#endregion

	#region TemplateModifierBase

	protected function modifierBodyImpl(mixed $value, array $params, Smarty_Internal_Template $smarty): string
	{
		return '';
	}

	#endregion
}
