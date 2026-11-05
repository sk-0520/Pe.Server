<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;

class TemplateFactory extends DiFactoryBase implements ITemplateFactory
{
	use DiFactoryTrait;

	#region ITemplateFactory

	#[Override]
	public function createTemplate(TemplateOptions $options): ITemplate
	{
		return $this->container->new(NodeTemplate::class, [
			TemplateOptions::class => $options,
			NodeComponentFactory::class => new NodeComponentFactory($this->container),
		]);
	}

	#endregion
}
