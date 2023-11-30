<?php


declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class RgbColorTest extends TestClass
{
	public function test_fromHtmlColorCode_basic_html()
	{
		$tests = [
			new Data(new RgbColor(0xff, 0xff, 0xff), "#ffffff"),
			new Data(new RgbColor(0xff, 0xff, 0xff), "#FFFFFF"),
			new Data(new RgbColor(0xab, 0xcd, 0xef, 0x40 / 2), "#abcdef40"),

			new Data(new RgbColor(0xff, 0xff, 0xff), "ffffff"),
			new Data(new RgbColor(0xff, 0xff, 0xff), "FFFFFF"),
			new Data(new RgbColor(0xab, 0xcd, 0xef, 0x40 / 2), "abcdef40"),

			new Data(new RgbColor(0xaa, 0xbb, 0xcc), "#abc"),
		];
		foreach ($tests as $test) {
			$actual = RgbColor::fromHtmlColorCode(Text::toLower($test->args[0]));
			$this->assertSame($test->expected->toHtml(), $actual->toHtml(), $test->str());
		}
	}

	public function test_fromHtmlColorCode_rgb()
	{
		$this->expectException(NotSupportedException::class);
		RgbColor::fromHtmlColorCode("rgb(1,1,1)");
		$this->fail();
	}

	public static function provider_fromHtmlColorCode_throw()
	{
		return [
			[''],
			[' '],
		];
	}

	/** @dataProvider provider_fromHtmlColorCode_throw */
	public function test_fromHtmlColorCode_throw($arg)
	{
		$this->expectException(ArgumentException::class);
		RgbColor::fromHtmlColorCode($arg);
		$this->fail();
	}

	public function test_serializable()
	{
		$tests = [
			new RgbColor(0x00, 0x00, 0x00),
			new RgbColor(0x12, 0x34, 0x56),
			new RgbColor(0x12, 0x34, 0x56, 0x78),
		];
		foreach ($tests as $test) {
			$s = serialize($test);
			$actual = unserialize($s);
			$this->assertSame($test->red, $actual->red, (string)$actual->red);
			$this->assertSame($test->green, $actual->green, (string)$actual->red);
			$this->assertSame($test->blue, $actual->blue, (string)$actual->blue);
			$this->assertSame($test->alpha, $actual->alpha, (string)$actual->alpha);
		}
	}
}
