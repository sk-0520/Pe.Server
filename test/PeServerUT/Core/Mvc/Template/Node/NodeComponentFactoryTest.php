<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\DI\DiContainer;
use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NodeComponentFactory;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\XmlBuilder;
use PeServerTest\TestClass;

class NodeComponentFactoryTest extends TestClass
{
	public function test_create_html(): void
	{
		$factory = new NodeComponentFactory(new DiContainer());
		$props = new LocalFactoryProps('a');

		$actual = $factory->create(LocalFactoryHtmlComponent::class, $props);

		$this->assertInstanceOf(LocalFactoryHtmlComponent::class, $actual);
		$this->assertSame($props, $actual->props);
		$this->assertInstanceOf(HtmlBuilder::class, $actual->getBuilder());
	}

	public function test_create_xml_fallback(): void
	{
		$factory = new NodeComponentFactory(new DiContainer());
		$props = new LocalFactoryProps('a');

		$actual = $factory->create(LocalFactoryXmlComponent::class, $props);

		$this->assertInstanceOf(LocalFactoryXmlComponent::class, $actual);
		$this->assertSame($props, $actual->props);
		$this->assertInstanceOf(XmlBuilder::class, $actual->builder);
	}
}

readonly class LocalFactoryProps extends Props
{
	public function __construct(public string $value)
	{
		//NOP
	}
}

/**
 * @extends HtmlComponentBase<LocalFactoryProps>
 */
class LocalFactoryHtmlComponent extends HtmlComponentBase
{
	public function __construct(LocalFactoryProps $other, HtmlBuilder $builder)
	{
		parent::__construct($other, $builder);
	}

	#[Override]
	protected function getNode(): INode
	{
		return NoneNode::get();
	}

	public function getBuilder(): object
	{
		return $this->builder;
	}
}

/**
 * @extends ComponentBase<LocalFactoryProps>
 */
class LocalFactoryXmlComponent extends ComponentBase
{
	public function __construct(LocalFactoryProps $other, public readonly XmlBuilder $builder)
	{
		parent::__construct($other);
	}

	#[Override]
	protected function getNode(): INode
	{
		return NoneNode::get();
	}
}
