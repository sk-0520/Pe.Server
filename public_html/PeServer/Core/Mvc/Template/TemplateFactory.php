<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Mvc\Template\SmartyTemplate;
use PeServer\Core\Mvc\Template\TemplateBase;
use PeServer\Core\Mvc\Template\TemplateOptions;

class TemplateFactory extends DiFactoryBase implements ITemplateFactory
{
	use DiFactoryTrait;

	#region ITemplateFactory

	public function createTemplate(TemplateOptions $options): TemplateBase
	{
		return $this->container->new(SmartyTemplate::class, [TemplateOptions::class => $options]);
	}

	#endregion
}
