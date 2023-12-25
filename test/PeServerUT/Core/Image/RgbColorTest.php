<?php


declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class RgbColorTest extends TestClass
{
	public static function provider_fromHtmlColorCode_basic_html()
	{
		return [
			[new RgbColor(0xff, 0xff, 0xff), "#ffffff"],
			[new RgbColor(0xff, 0xff, 0xff), "#FFFFFF"],
			[new RgbColor(0xab, 0xcd, 0xef, 0x40 / 2), "#abcdef40"],

			[new RgbColor(0xff, 0xff, 0xff), "ffffff"],
			[new RgbColor(0xff, 0xff, 0xff), "FFFFFF"],
			[new RgbColor(0xab, 0xcd, 0xef, 0x40 / 2), "abcdef40"],

			[new RgbColor(0xaa, 0xbb, 0xcc), "#abc"],
		];
	}

	#[DataProvider('provider_fromHtmlColorCode_basic_html')]
	public function test_fromHtmlColorCode_basic_html(RgbColor $expected, string $htmlColor)
	{
		$actual = RgbColor::fromHtmlColorCode(Text::toLower($htmlColor));
		$this->assertSame($expected->toHtml(), $actual->toHtml());
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

	#[DataProvider('provider_fromHtmlColorCode_throw')]
	public function test_fromHtmlColorCode_throw($arg)
	{
		$this->expectException(ArgumentException::class);
		RgbColor::fromHtmlColorCode($arg);
		$this->fail();
	}

	public static function provider_serializable()
	{
		return [
			[new RgbColor(0x00, 0x00, 0x00)],
			[new RgbColor(0x12, 0x34, 0x56)],
			[new RgbColor(0x12, 0x34, 0x56, 0x78)],
		];
	}

	#[DataProvider('provider_serializable')]
	public function test_serializable(RgbColor $test)
	{
		$s = serialize($test);
		$actual = unserialize($s);
		$this->assertSame($test->red, $actual->red, (string)$actual->red);
		$this->assertSame($test->green, $actual->green, (string)$actual->red);
		$this->assertSame($test->blue, $actual->blue, (string)$actual->blue);
		$this->assertSame($test->alpha, $actual->alpha, (string)$actual->alpha);
	}
}
