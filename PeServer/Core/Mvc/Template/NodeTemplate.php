<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Store\Stores;

class NodeTemplate extends TemplateBase
{
	public function __construct(
		TemplateOptions $options,
		protected Stores $stores,
		protected Environment $environment,
		protected NodeComponentFactory $componentFactory
	) {
		parent::__construct($options);
	}

	#[Override]
	public function build(string $templateName, TemplateParameter $parameter): string
	{
		throw new \RuntimeException('Not implemented yet.');
	}
}
