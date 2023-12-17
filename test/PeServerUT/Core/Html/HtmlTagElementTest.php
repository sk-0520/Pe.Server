<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Throws\HtmlAttributeException;
use PeServer\Core\Throws\HtmlDocumentException;
use PeServerTest\TestClass;

class HtmlTagElementTest extends TestClass
{
	public function test_attribute_string()
	{
		$doc = new HtmlDocument();
		$element = $doc->addTagElement('p');

		$this->assertFalse($element->hasAttribute('attribute'));
		$try1 = $element->tryGetAttribute('attribute', $result1);
		$this->isNull($result1);
		$this->assertFalse($try1);

		try {
			$element->getAttribute('attribute-nothing');
			$this->fail();
		} catch (HtmlAttributeException) {
			$this->success();
		}

		$element->setAttribute('attribute', 'value');

		$this->isTrue($element->hasAttribute('attribute'));
		$this->assertSame('value', $element->getAttribute('attribute'));

		$try2 = $element->tryGetAttribute('attribute', $result2);
		$this->isTrue($try2);
		$this->assertSame('value', $result2);
	}

	public function test_attribute_boolean()
	{
		$doc = new HtmlDocument();
		$element = $doc->addTagElement('p');
		$this->assertFalse($element->isAttribute('attribute'));

		$element->setAttribute('attribute', true);
		$this->assertTrue($element->isAttribute('attribute'));

		$element->setAttribute('attribute', false);
		$this->assertFalse($element->isAttribute('attribute'));
	}

	public function test_attribute_throw()
	{
		$doc = new HtmlDocument();
		$element = $doc->addTagElement('p');

		$this->expectException(HtmlAttributeException::class);

		$element->setAttribute('', '');

		$this->fail();
	}

	public function test_classList()
	{
		$doc = new HtmlDocument();
		$element = $doc->addTagElement('p');

		$this->assertSame([], $element->getClassList());

		$element->addClass('CLASS');
		$this->assertSame(['CLASS'], $element->getClassList());

		$element->addClass('CLASS');
		$this->assertSame(['CLASS'], $element->getClassList());

		$classes = $element->getClassList();
		$classes[] = 'CLASS2';
		$element->setClassList($classes);
		$this->assertSame(['CLASS', 'CLASS2'], $element->getClassList());

		$element->setClassList(['CLASS', 'CLASS2', 'CLASS2']);
		$this->assertSame(['CLASS', 'CLASS2'], $element->getClassList());

		$element->removeClass('CLASS');
		$this->assertSame(['CLASS2'], $element->getClassList());

		$element->removeClass('CLASS');
		$this->assertSame(['CLASS2'], $element->getClassList());

		$element->removeClass('CLASS2');
		$this->assertSame([], $element->getClassList());
	}
}
