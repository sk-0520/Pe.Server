<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\XmlBuilder;

class NodeComponentFactory extends DiFactoryBase
{
	use DiFactoryTrait;

	/**
	 * ノードテンプレートを実体化。
	 *
	 * @template TProps of \PeServer\Core\Mvc\Template\Node\Props
	 * @template TComponent of \PeServer\Core\Mvc\Template\Node\ComponentBase<TProps>
	 * @param string $component
	 * @phpstan-param class-string<TComponent> $component
	 * @return ComponentBase
	 * @phpstan-return TComponent
	 */
	public function create(string $component, Props $props): ComponentBase
	{
		$builder = is_subclass_of($component, HtmlComponentBase::class)
			? new HtmlBuilder()
			: new XmlBuilder();
		$arguments = [
			'$props' => $props,
			get_class($builder) => $builder
		];

		return $this->container->new($component, $arguments);
	}
}
