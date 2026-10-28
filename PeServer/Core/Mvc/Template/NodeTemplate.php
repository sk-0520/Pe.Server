<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template;

use Override;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;
use PeServer\Core\Mvc\Template\Node\XmlBuilder;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\ArgumentException;

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

	/**
	 * 描画。
	 *
	 * @param string $templateName 描画する `ComponentBase` のクラス名。
	 */
	#[Override]
	public function build(string $templateName, TemplateParameter $parameter): string
	{
		// @phpstan-ignore argument.type, argument.templateType
		$component = $this->componentFactory->create($templateName, new RootProps($parameter, $this->stores, $this->environment));
		$builder = is_subclass_of($templateName, HtmlComponentBase::class) ? new HtmlBuilder() : new XmlBuilder();

		return $builder->build($component);
	}
}
