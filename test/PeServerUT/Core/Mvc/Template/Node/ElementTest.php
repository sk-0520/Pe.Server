<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Iterator;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\INode;

class ElementTest extends TestClass
{
	public static function provider_toString()
	{
		return [
			['<tag attr="value" />', NoneNode::get(), new ElementOptions(true, true)],
			['<tag attr="value">inline</tag>', new TextNode("inline"), new ElementOptions(true, false)],
			['<tag attr="value" />', new TextNode("ignore content"), new ElementOptions(true, true)],
			['<tag attr="value"></tag>', NoneNode::get(), new ElementOptions(true, false)],
			['<tag attr="value"></tag>', NoneNode::get(), new ElementOptions(false, false)],
			['<tag attr="value">aaa</tag>', new TextNode("aaa"), new ElementOptions(false, false)],
			['<tag attr="value"><tag attr="value">bbb</tag></tag>', new Fragment(new LocalSimleElement(new TextNode("bbb"), new ElementOptions(false, false))), new ElementOptions(false, false)],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, INode $content, ElementOptions $options): void
	{
		$test = new LocalSimleElement($content, $options);

		$this->assertSame($expected, (string)$test);
	}
}

class LocalSimleElement extends Element
{
	public function __construct(INode $content, ElementOptions $options)
	{
		parent::__construct("tag", new class () extends AttributesBase {
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
