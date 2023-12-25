<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\SizeConverter;
use PHPUnit\Framework\Attributes\DataProvider;

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
}
