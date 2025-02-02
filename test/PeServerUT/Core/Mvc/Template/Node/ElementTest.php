<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServerTest\TestClass;

class HTMLElementTest extends TestClass
{
	public function test_constructor()
	{
		$actual = new Element("name", new Attributes([]), new Content([]), new Props(), new ElementOptions(false, false));
		$this->assertSame("name", $actual->tagName);
	}

	public function test___toString_selfClosing_no_attr()
	{
		$element = new Element("name", new Attributes([]), new Content([]), new Props(), new ElementOptions(false, true));
		$actual = (string)$element;
		$this->assertSame("<name />", $actual);
	}

	public function test___toString_selfClosing_with_attr()
	{
		$element = new Element("name", new Attributes([
			"key" => "value",
			"name-only" => null,
		]), new NoneContent(), new Props(), new ElementOptions(false, true));
		$actual = (string)$element;
		$this->assertSame("<name key=\"value\" name-only />", $actual);
	}

	public function test___toString_selfClosing_child()
	{
		$element = new Element("name", new Attributes([]), new Content([
			new Element("child", new Attributes([]), new Content([]), new Props(), new ElementOptions(false, true))
		]), new Props(), new ElementOptions(false, true));
		$actual = (string)$element;
		$this->assertSame("<name />", $actual);
	}

	public function test___toString_not_selfClosing_no_attr()
	{
		$element = new Element("name", new Attributes([]), new Content([]), new Props(), new ElementOptions(false, false));
		$actual = (string)$element;
		$this->assertSame("<name></name>", $actual);
	}

	public function test___toString_not_selfClosing_with_attr()
	{
		$element = new Element("name", new Attributes([
			"key" => "value",
			"name-only" => null,
		]), new NoneContent(), new Props(), new ElementOptions(false, false));
		$actual = (string)$element;
		$this->assertSame("<name key=\"value\" name-only></name>", $actual);
	}

	public function test___toString_not_selfClosing_child_1()
	{
		$element = new Element("name", new Attributes([]), new Content(
			[
				new Element("child", new Attributes([]), new Content([]), new Props(), new ElementOptions(false, true))
			]
		), new Props(), new ElementOptions(false, false));
		$actual = (string)$element;
		$this->assertSame("<name><child /></name>", $actual);
	}

	public function test___toString_not_selfClosing_child_2()
	{
		$element = new Element("name", new Attributes([]), new Content([
			new Element("child", new Attributes([]), new Content([new TextNode("text")]), new Props(), new ElementOptions(false, false))
		]), new Props(), new ElementOptions(false, false));
		$actual = (string)$element;
		$this->assertSame("<name><child>text</child></name>", $actual);
	}
}
