<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;

class HTMLAudioAttributesTest extends TestClass
{
	public function test_autoPlay_false()
	{
		$attributes = new HTMLAudioAttributes(autoPlay: false);
		$actual = $attributes->build("autoPlay");
		$this->assertSame("", $actual);
	}

	public function test_autoPlay_true()
	{
		$attributes = new HTMLAudioAttributes(autoPlay: true);
		$actual = $attributes->build("autoPlay");
		$this->assertSame("autoplay", $actual);
	}
}
