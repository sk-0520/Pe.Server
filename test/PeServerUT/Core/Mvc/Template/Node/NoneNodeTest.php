<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServerTest\TestClass;

class NoneNodeTest extends TestClass
{
	public function test_get_singleton(): void
	{
		$this->assertSame(NoneNode::get(), NoneNode::get());
	}

	public function test_build(): void
	{
		$test = NoneNode::get();
		$this->assertSame('', $test->build(0, BuildOptions::default()));
		$this->assertSame('', (string)$test);
		$this->assertFalse($test->hasChildren());
	}
}
