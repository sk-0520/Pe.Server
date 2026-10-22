<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAudioAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ControlsList;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ControlsListValues;

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

	public static function provider_controlsList(): array
	{
		return [
			['controlslist="nodownload"', ControlsList::NoDownload],
			['controlslist=""', new ControlsListValues([])],
			['controlslist="nodownload"', new ControlsListValues(ControlsList::NoDownload)],
			['controlslist="nodownload noplaybackrate noremoteplayback"', new ControlsListValues([ControlsList::NoDownload, ControlsList::NoPlayBackrate, ControlsList::NoRemotePlayback])],
		];
	}

	#[DataProvider('provider_controlsList')]
	public function test_controlsList(string $expected, ControlsList|ControlsListValues $controlsList)
	{
		$attributes = new HTMLAudioAttributes(autoPlay: true, controlsList: $controlsList);
		$actual = $attributes->build("controlsList");
		$this->assertSame($expected, $actual);
	}
}
