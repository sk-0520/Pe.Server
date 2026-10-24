<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Iterator;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\TextContent;

class ElementTest extends TestClass
{
	public static function provider_toString()
	{
		return [
			['<tag attr="value" />', new Content([]), new ElementOptions(true, true)],
			['<tag attr="value">inline</tag>', new TextContent("inline"), new ElementOptions(true, false)],
			['<tag attr="value" />', new TextContent("ignore content"), new ElementOptions(true, true)],
			['<tag attr="value"></tag>', new Content([]), new ElementOptions(true, false)],
			['<tag attr="value"></tag>', new Content([]), new ElementOptions(false, false)],
			['<tag attr="value">aaa</tag>', new TextContent("aaa"), new ElementOptions(false, false)],
			['<tag attr="value"><tag attr="value">bbb</tag></tag>', new Content(new LocalSimleElement(new TextContent("bbb"), new ElementOptions(false, false))), new ElementOptions(false, false)],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, Content $content, ElementOptions $options): void
	{
		$test = new LocalSimleElement($content, $options);

		$this->assertSame($expected, (string)$test);
	}
}

class LocalSimleElement extends Element
{
	public function __construct(Content $content, ElementOptions $options)
	{
		parent::__construct("tag", new class() extends AttributesBase {
			public function getNames(): Iterator
			{
				yield "attr";
			}

			public function build(string $name, AttributeValueMark $mark = AttributeValueMark::DoubleQuote): string
			{
				return $name . '=' . $this->quoteValue("value", $mark);
			}
		}, $content, $options);
	}
}
