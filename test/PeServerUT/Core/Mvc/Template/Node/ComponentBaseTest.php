<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServerTest\TestClass;

readonly class LocalGreetingProps extends Props
{
	public function __construct(public string $name)
	{
		//NOP
	}
}

/**
 * @extends HtmlComponentBase<LocalGreetingProps>
 */
class LocalGreetingComponent extends HtmlComponentBase
{
	public int $count = 0;

	public function __construct(LocalGreetingProps $props, HtmlBuilder $builder)
	{
		parent::__construct($props, $builder);
	}

	#[Override]
	protected function getNode(): INode
	{
		$this->count++;
		return $this->html->div(new HtmlGlobalAttributes(), new TextNode($this->props->name));
	}
}

class ComponentBaseTest extends TestClass
{
	public function test_toString(): void
	{
		$test = new LocalGreetingComponent(new LocalGreetingProps('<a>'), new HtmlBuilder());
		$this->assertSame('<div>&lt;a&gt;</div>', (string)$test);
		$this->assertTrue($test->hasChildren());
	}

	public function test_getNode_cached(): void
	{
		$test = new LocalGreetingComponent(new LocalGreetingProps('a'), new HtmlBuilder());
		$test->hasChildren();
		(string)$test;
		(string)$test;
		$this->assertSame(1, $test->count);
	}
}
