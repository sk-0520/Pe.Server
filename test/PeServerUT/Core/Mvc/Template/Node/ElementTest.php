<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use Iterator;
use Override;
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
			['<tag attr="value"><tag attr="value" /></tag>', new LocalSimleElement(NoneNode::get(), new ElementOptions(true, true)), new ElementOptions(false, false)],
			['<tag attr="value"><tag attr="value"></tag></tag>', new LocalSimleElement(NoneNode::get(), new ElementOptions(false, false)), new ElementOptions(false, false)],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, INode $content, ElementOptions $options): void
	{
		$test = new LocalSimleElement($content, $options);

		$this->assertSame($expected, (string)$test);
	}

	public function test_build_uses_overridden_tag_methods(): void
	{
		foreach ([false, true] as $selfClosing) {
			$options = new ElementOptions(true, $selfClosing);
			$buildOptions = BuildOptions::default();
			$element = new class (new TextNode('child'), $options) extends LocalSimleElement {
				public array $calls = [];

				#[Override]
				protected function buildAttribute(string $tagName, ElementOptions $options, BuildOptions $buildOptions): string
				{
					$this->calls[] = ['attribute', $tagName, $options, $buildOptions];
					return parent::buildAttribute($tagName, $options, $buildOptions) . ' extra="yes"';
				}

				#[Override]
				protected function buildStartTag(string $tagName, string $builtAttributes, ElementOptions $options, BuildOptions $buildOptions): string
				{
					$this->calls[] = ['start', $tagName, $options, $buildOptions];
					return '[' . parent::buildStartTag($tagName, $builtAttributes, $options, $buildOptions);
				}

				#[Override]
				protected function buildEndTag(string $tagName, ElementOptions $options, BuildOptions $buildOptions): string
				{
					$this->calls[] = ['end', $tagName, $options, $buildOptions];
					return parent::buildEndTag($tagName, $options, $buildOptions) . ']';
				}
			};

			$this->assertSame(
				$selfClosing ? '[<tag attr="value" extra="yes" />]' : '[<tag attr="value" extra="yes">child</tag>]',
				$element->build(0, $buildOptions)
			);
			$this->assertSame([
				['attribute', 'tag', $options, $buildOptions],
				['start', 'tag', $options, $buildOptions],
				['end', 'tag', $options, $buildOptions],
			], $element->calls);
		}
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
