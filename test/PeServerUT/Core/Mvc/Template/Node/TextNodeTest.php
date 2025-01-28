<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServerTest\TestClass;

class TextNodeTest extends TestClass
{
	public function test___toString_default()
	{
		$node = new TextNode("<あいうえお>");
		$actual = (string)$node;
		$this->assertSame("&lt;あいうえお&gt;", $actual);
	}

	public function test___toString_raw()
	{
		$node = new TextNode("<あいうえお>", TextNode::ESCAPE_RAW);
		$actual = (string)$node;
		$this->assertSame("<あいうえお>", $actual);
	}
}
