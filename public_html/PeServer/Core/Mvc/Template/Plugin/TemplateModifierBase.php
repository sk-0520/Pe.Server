<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;

abstract class TemplateModifierBase extends TemplatePluginBase implements ITemplateModifier
{
	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region function

	protected abstract function modifierBodyImpl(mixed $value, mixed ...$params): mixed;

	#region ITemplateModifier

	public abstract function getModifierName(): string;

	public function modifierBody(mixed $value, mixed ...$params): mixed
	{
		return $this->modifierBodyImpl($value, $params);
	}

	#endregion
}
