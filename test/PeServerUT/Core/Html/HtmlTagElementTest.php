<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Throws\HtmlAttributeException;
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
			$element->getAttribute('attribute');
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
}
