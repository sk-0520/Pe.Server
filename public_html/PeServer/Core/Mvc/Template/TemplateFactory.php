<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Mvc\Template\SmartyTemplate;
use PeServer\Core\Mvc\Template\TemplateBase;

class TemplateFactory extends DiFactoryBase implements ITemplateFactory
{
	public function __construct(IDiContainer $container)
	{
		parent::__construct($container);
	}

	#region ITemplateFactory

	public function createTemplate(TemplateOptions $options): TemplateBase
	{
		return $this->container->new(SmartyTemplate::class, [TemplateOptions::class => $options]);
	}

	#endregion
}
