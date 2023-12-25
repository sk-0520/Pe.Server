<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Size;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class SizeTest extends TestClass
{
	public static function provider_constructor_throw()
	{
		return [
			[0, 0],
			[1, 0],
			[0, 1],
			[-1, -1],
			[+1, -1],
			[-1, +1],
		];
	}

	#[DataProvider('provider_constructor_throw')]
	public function test_constructor_throw($width, $height)
	{
		$this->expectException(ArgumentException::class);
		new Size($width, $height);
		$this->fail();
	}

	public static function provider_serializable()
	{
		return [
			[new Size(123, 456)],
		];
	}

	#[DataProvider('provider_serializable')]
	public function test_serializable(Size $test)
	{
		$s = serialize($test);
		$actual = unserialize($s);
		$this->assertSame($test->width, $actual->width, (string)$actual->width);
		$this->assertSame($test->height, $actual->height, (string)$actual->height);
	}

	public function test___toString()
	{
		$size = new Size(1, 2);
		$this->assertSame('PeServer\Core\Image\Size(width:1,height:2)', (string)$size);
	}
}
