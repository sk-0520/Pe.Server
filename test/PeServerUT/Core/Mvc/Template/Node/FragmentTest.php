<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServerTest\TestClass;

class FragmentTest extends TestClass
{
	public function test_build_single_node(): void
	{
		$test = new Fragment(new TextNode('a'));
		$this->assertSame('a', $test->build(0, BuildOptions::default()));
		$this->assertTrue($test->hasChildren());
	}

	public function test_build_multiple_nodes_in_order(): void
	{
		$test = new Fragment([
			new TextNode('a'),
			new TextNode('<b>', TextNode::ESCAPE_RAW),
			new TextNode('&'),
		]);
		$this->assertSame('a<b>&amp;', $test->build(0, BuildOptions::default()));
		$this->assertSame('a<b>&amp;', (string)$test);
	}

	public function test_build_nested(): void
	{
		$test = new Fragment([
			new TextNode('a'),
			new Fragment([new TextNode('b'), new TextNode('c')]),
		]);
		$this->assertSame('abc', $test->build(0, BuildOptions::default()));
	}

	public function test_build_empty(): void
	{
		$test = new Fragment([]);
		$this->assertSame('', $test->build(0, BuildOptions::default()));
		$this->assertFalse($test->hasChildren());
	}

	public function test_build_none_node(): void
	{
		$test = new Fragment([NoneNode::get(), new TextNode('a')]);
		$this->assertSame('a', $test->build(0, BuildOptions::default()));
	}
}
