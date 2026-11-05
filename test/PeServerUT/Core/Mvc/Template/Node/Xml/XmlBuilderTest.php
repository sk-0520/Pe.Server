<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Xml;

use PeServer\Core\Encoding;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\NodeBuilder;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\Xml\XmlBuilder;
use PeServerTest\TestClass;

class XmlBuilderTest extends TestClass
{
	public function test_NodeBuilder_build(): void
	{
		$test = new NodeBuilder();
		$this->assertSame('a', $test->build(new TextNode('a')));
		$this->assertSame('a', $test->build(new TextNode('a'), new BuildOptions(BuildOptions::FORMAT_NONE, '')));
	}

	public function test_build_default(): void
	{
		$test = new XmlBuilder();
		$encoding = Encoding::getUtf8()->name;
		$this->assertSame('<?xml version="1.0" encoding="' . $encoding . '"?>' . PHP_EOL . 'a', $test->build(new TextNode('a')));
	}

	public function test_build_standalone(): void
	{
		$encoding = Encoding::getUtf8()->name;

		$test = new XmlBuilder();
		$test->standalone = true;
		$this->assertSame('<?xml version="1.0" encoding="' . $encoding . '" standalone="yes"?>' . PHP_EOL . 'a', $test->build(new TextNode('a')));

		$test->standalone = false;
		$this->assertSame('<?xml version="1.0" encoding="' . $encoding . '" standalone="no"?>' . PHP_EOL . 'a', $test->build(new TextNode('a')));
	}

	public function test_build_version(): void
	{
		$test = new XmlBuilder();
		$test->version = '1.1';
		$encoding = Encoding::getUtf8()->name;
		$this->assertSame('<?xml version="1.1" encoding="' . $encoding . '"?>' . PHP_EOL . 'a', $test->build(new TextNode('a')));
	}
}
