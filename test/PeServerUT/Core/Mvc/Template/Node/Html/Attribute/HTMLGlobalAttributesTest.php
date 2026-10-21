<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocapitalize;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Autocorrect;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Contenteditable;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Direction;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\InputMode;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Popover;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Translate;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\VirtualKeyboardPolicy;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class HtmlGlobalAttributesTest extends TestClass
{
	public static function provider_getNames()
	{
		return [
			[
				[],
				new HtmlGlobalAttributes(),
			],
			[
				['id'],
				new HtmlGlobalAttributes(data: new DataAttributes(), id: 'test'),
			],
			[
				[
					'accesskey',
					'anchor',
					'autoCapitalize',
					'autoCorrect',
					'autoFocus',
					'className',
					'contentEditable',
					'data',
					'dir',
					'draggable',
					'enterKeyHint',
					'hidden',
					'id',
					'inert',
					'inputMode',
					'is',
					'itemId',
					'itemProp',
					'itemRef',
					'itemType',
					'lang',
					'nonce',
					'part',
					'popover',
					'role',
					'slot',
					'spellcheck',
					'style',
					'tabIndex',
					'title',
					'translate',
					'virtualKeyboardPolicy',
					'writingSuggestions',
				],
				new HtmlGlobalAttributes(
					accesskey: '',
					anchor: 'anchor-id',
					autoCapitalize: Autocapitalize::Sentences,
					autoCorrect: Autocorrect::Off,
					autoFocus: false,
					className: 'example',
					contentEditable: Contenteditable::True,
					data: new DataAttributes(['test' => 'value']),
					dir: Direction::LeftToRight,
					draggable: false,
					enterKeyHint: 'go',
					hidden: false,
					id: 'element-id',
					inert: false,
					inputMode: InputMode::Text,
					is: 'custom-element',
					itemId: 'item-id',
					itemProp: 'name',
					itemRef: 'reference',
					itemType: 'type',
					lang: Bcp47::Japanese,
					nonce: 'nonce-value',
					part: 'part-name',
					popover: Popover::Auto,
					role: 'button',
					slot: 'slot-name',
					spellcheck: false,
					style: 'color: red',
					tabIndex: 0,
					title: 'title',
					translate: Translate::No,
					virtualKeyboardPolicy: VirtualKeyboardPolicy::Manual,
					writingSuggestions: false,
				),
			],
		];
	}

	#[DataProvider('provider_getNames')]
	public function test_getNames(array $expected, HtmlGlobalAttributes $test): void
	{
		$actual = iterator_to_array($test->getNames());
		$this->assertSame($expected, $actual);
	}

	public static function provider_build()
	{
		return [
			[
				'id="example"',
				new HtmlGlobalAttributes(id: 'example'),
				'id',
				AttributeValueMark::DoubleQuote,
			],
			[
				"id='it&#39;s'",
				new HtmlGlobalAttributes(id: "it's"),
				'id',
				AttributeValueMark::SingleQuote,
			],
			[
				'hidden="false"',
				new HtmlGlobalAttributes(hidden: false),
				'hidden',
				AttributeValueMark::DoubleQuote,
			],
			[
				'tabindex="-1"',
				new HtmlGlobalAttributes(tabIndex: -1),
				'tabIndex',
				AttributeValueMark::DoubleQuote,
			],
			[
				'dir="rtl"',
				new HtmlGlobalAttributes(dir: Direction::RightToLeft),
				'dir',
				AttributeValueMark::DoubleQuote,
			],
			[
				'autocorrect=""',
				new HtmlGlobalAttributes(autoCorrect: Autocorrect::Default),
				'autoCorrect',
				AttributeValueMark::DoubleQuote,
			],
			[
				'data-first="A" data-second="B"',
				new HtmlGlobalAttributes(data: new DataAttributes(['first' => 'A', 'second' => 'B'])),
				'data',
				AttributeValueMark::DoubleQuote,
			],
			[
				"data-first='it&#39;s'",
				new HtmlGlobalAttributes(data: new DataAttributes(['first' => "it's"])),
				'data',
				AttributeValueMark::SingleQuote,
			],
		];
	}

	#[DataProvider('provider_build')]
	public function test_build(string $expected, HtmlGlobalAttributes $test, string $name, AttributeValueMark $mark): void
	{
		$actual = $test->build($name, $mark);
		$this->assertSame($expected, $actual);
	}
}
