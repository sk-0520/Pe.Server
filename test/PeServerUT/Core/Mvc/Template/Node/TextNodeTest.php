<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\BuildOptions;

class TextNodeTest extends TestClass
{
	public static function provider_build(): array
	{
		return [
			['text', 'text', TextNode::ESCAPE_DEFAULT, BuildOptions::default()],
			['&lt;b&gt;text&lt;/b&gt;', '<b>text</b>', TextNode::ESCAPE_DEFAULT, BuildOptions::default()],
			['<b>text</b>', '<b>text</b>', TextNode::ESCAPE_RAW, BuildOptions::default()],
		];
	}
	#[DataProvider('provider_build')]
	public function test_build(string $expected, string $text, int $escape, BuildOptions $buildOptions): void
	{
		$test = new TextNode($text, $escape);
		$actual = $test->build(0, $buildOptions);
		$this->assertSame($expected, $actual);
	}
}
