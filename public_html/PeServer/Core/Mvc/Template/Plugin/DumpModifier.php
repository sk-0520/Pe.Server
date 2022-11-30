<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Mvc\Template\Plugin\TemplateModifierBase;
use PeServer\Core\Mvc\Template\Plugin\ITemplateModifier;
use PeServer\Core\Text;

class DumpModifier extends TemplateModifierBase implements ITemplateModifier
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region ITemplateModifier

	public function getModifierName(): string
	{
		return 'dump';
	}

	#endregion

	#region TemplateModifierBase

	protected function modifierBodyImpl(mixed $value, mixed ...$params): string
	{
		return Text::dump($value);
	}

	#endregion
}
