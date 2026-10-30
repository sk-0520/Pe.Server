<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\NodeContextBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\Xml\XmlNodeContext;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Xml\XmlBuilder;

class NodeComponentFactory extends DiFactoryBase
{
	use DiFactoryTrait;

	#region variable

	/** @var array<string,HtmlBuilder|XmlBuilder> */
	private array $cache = [];

	#endregion

	#region function

	public function getBuilder(string $component): HtmlBuilder|XmlBuilder
	{
		if (isset($this->cache[$component])) {
			return $this->cache[$component];
		}

		// Html 系以外は Xml をフォールバックとする
		$builder = is_subclass_of($component, HtmlComponentBase::class)
			? new HtmlBuilder()
			: new XmlBuilder();

		$this->cache[$component] = $builder;
		return $builder;
	}

	/**
	 * コンポーネントに対応するノードコンテキストを作成する。
	 *
	 * @param string $component
	 * @return HtmlNodeContext|XmlNodeContext
	 */
	public function createContext(string $component): HtmlNodeContext|XmlNodeContext
	{
		$builder = $this->getBuilder($component);
		$nodeContextClassName = $builder instanceof HtmlBuilder
			? HtmlNodeContext::class
			: XmlNodeContext::class;

		//ここでcontextを作成するべし
		// あれ これコンテキスト作る場所か？ あーわからん

		return $this->container->new($nodeContextClassName, [
			-1 => $builder,
		]);
	}

	/**
	 * ノードテンプレートを実体化。
	 *
	 * @template TProps of \PeServer\Core\Mvc\Template\Node\Props
	 * @template TComponent of \PeServer\Core\Mvc\Template\Node\ComponentBase<TProps>
	 * @param string $component
	 * @phpstan-param class-string<TComponent> $component
	 * @param Props $props
	 * @phpstan-param TProps $props
	 * @param NodeContextBase $context
	 * @return ComponentBase
	 * @phpstan-return TComponent
	 */
	public function create(string $component, Props $props, NodeContextBase $context): ComponentBase
	{
		$arguments = [
			-1 => $props,
			-2 => $context,
		];

		return $this->container->new($component, $arguments);
	}

	#endregion
}
