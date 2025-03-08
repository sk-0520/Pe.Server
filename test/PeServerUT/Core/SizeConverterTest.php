<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\SizeConverter;
use PeServer\Core\Throws\ArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;

class SizeConverterTest extends TestClass
{
	public static function provider_convertHumanReadableByte()
	{
		return [
			['0 byte', 0],
			['1 byte', 1],
			['1023 byte', 1023],
			['1 KB', 1024],
		];
	}

	#[DataProvider('provider_convertHumanReadableByte')]
	public function test_convertHumanReadableByte(string $expected, int $byteSize, string $sizeFormat = '{i_size} {unit}')
	{
		$sc = new SizeConverter();
		$actual = $sc->convertHumanReadableByte($byteSize, $sizeFormat);
		$this->assertSame($expected, $actual);
	}

	#[TestWith(["0 byte", 0])]
	#[TestWith(["1 KB", 1024])]
	#[TestWith(["1 MB", 1024 * 1024])]
	#[TestWith(["10 MB", 10 * 1024 * 1024])]
	#[TestWith(["1024 MB", 1024 * 1024 * 1024])]
	#[TestWith(["2048 MB", 2 * 1024 * 1024 * 1024])]
	public function test_convertHumanReadableByte_limit(string $expected, int $byteSize, string $sizeFormat = '{i_size} {unit}')
	{
		$sc = new SizeConverter(1024, ["byte", "KB", "MB"]);
		$actual = $sc->convertHumanReadableByte($byteSize, $sizeFormat);
		$this->assertSame($expected, $actual);
	}


	#[TestWith([2, 0, 2])]
	#[TestWith([2, 1, 2])]
	#[TestWith([2, 2, 2])]
	#[TestWith([4, 3, 2])]
	#[TestWith([4, 4, 2])]
	#[TestWith([8, 7, 2])]
	#[TestWith([8, 8, 2])]
	#[TestWith([10, 9, 2])]
	#[TestWith([8, 0, 8])]
	#[TestWith([8, 1, 8])]
	#[TestWith([8, 7, 8])]
	#[TestWith([8, 8, 8])]
	#[TestWith([16, 9, 8])]
	#[TestWith([16, 15, 8])]
	#[TestWith([16, 16, 8])]
	#[TestWith([24, 17, 8])]
	public function test_ceiling(int $expected, int $input, int $base)
	{
		$sc = new SizeConverter();
		$actual = $sc->ceiling($input, $base);
		$this->assertSame($expected, $actual);
	}

	public function test_ceiling_throw()
	{
		$sc = new SizeConverter();
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage("base");
		$sc->ceiling(2, 0);
	}
}
