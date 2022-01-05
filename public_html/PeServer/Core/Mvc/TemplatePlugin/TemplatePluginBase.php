<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;


abstract class TemplatePluginBase
{
	protected TemplatePluginArgument $argument;

	protected function __construct(TemplatePluginArgument $argument)
	{
		$this->argument = $argument;
	}
}
