<?php


declare(strict_types=1);

namespace PeServerTest\Core\Image;

use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\TestClass;
use PeServerTest\Data;

class RgbColorTest extends TestClass
{
	public function test_fromHtmlColorCode_basic_html()
	{
		$tests = [
			new Data(new RgbColor(0xff, 0xff, 0xff), "#ffffff"),
			new Data(new RgbColor(0xff, 0xff, 0xff), "#FFFFFF"),
			new Data(new RgbColor(0xab, 0xcd, 0xef, 0x40), "#abcdef40"),

			new Data(new RgbColor(0xff, 0xff, 0xff), "ffffff"),
			new Data(new RgbColor(0xff, 0xff, 0xff), "FFFFFF"),
			new Data(new RgbColor(0xab, 0xcd, 0xef, 0x40), "abcdef40"),

			new Data(new RgbColor(0xaa, 0xbb, 0xcc), "#abc"),
		];
		foreach ($tests as $test) {
			$actual = RgbColor::fromHtmlColorCode(StringUtility::toLower($test->args[0]));
			$this->assertEquals($test->expected->toHtml(), $actual->toHtml(), $test->str());
		}
	}

	public function test_fromHtmlColorCode_rgb()
	{
		$this->expectException(NotSupportedException::class);
		RgbColor::fromHtmlColorCode("rgb(1,1,1)");
		$this->fail();
	}
}
