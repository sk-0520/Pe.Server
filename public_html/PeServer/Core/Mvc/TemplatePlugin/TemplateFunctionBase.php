<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;


abstract class TemplateFunctionBase extends TemplatePluginBase implements ITemplateFunction
{
	protected function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}
}
