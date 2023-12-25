<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\TrueKeeper;
use PeServer\Core\Uuid;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class UuidTest extends TestClass
{
	public static function provider_isEqualGuid()
	{
		$input = '70457e15-8928-4418-9b27-30bd46b1ae30';

		return [
			[true, $input, '70457e15-8928-4418-9b27-30bd46b1ae30'],
			[true, $input, '70457E15-8928-4418-9B27-30BD46B1AE30'],

			[true, $input, '70457e15892844189b2730bd46b1ae30'],
			[true, $input, '70457E15892844189B2730BD46B1AE30'],

			[true, $input, '{70457e15-8928-4418-9b27-30bd46b1ae30}'],
			[true, $input, '{70457E15-8928-4418-9B27-30BD46B1AE30}'],

			[true, $input, '{70457e15892844189b2730bd46b1ae30}'],
			[true, $input, '{70457E15892844189B2730BD46B1AE30}'],
		];
	}

	#[DataProvider('provider_isEqualGuid')]
	public function test_isEqualGuid(bool $expected, string $a, string $b)
	{
		$actual = Uuid::isEqualGuid($a, $b);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isGuid()
	{
		return [
			[true, '70457e15-8928-4418-9b27-30bd46b1ae30'],
			[true, '70457E15-8928-4418-9B27-30BD46B1AE30'],
			[true, '70457e15892844189b2730bd46b1ae30'],
			[true, '70457E15892844189B2730BD46B1AE30'],
			[true, '{70457e15-8928-4418-9b27-30bd46b1ae30}'],
			[true, '{70457E15-8928-4418-9B27-30BD46B1AE30}'],
			[true, '{70457e15892844189b2730bd46b1ae30}'],
			[true, '{70457E15892844189B2730BD46B1AE30}'],
			[false, ''],
			[false, 'G0457e15-8928-4418-9b27-30bd46b1ae30'],
		];
	}

	#[DataProvider('provider_isGuid')]
	public function test_isGuid(bool $expected, string $value)
	{
		$actual = Uuid::isGuid($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_adjustGuid()
	{
		$expected = '70457e15-8928-4418-9b27-30bd46b1ae30';

		return [
			[$expected, '70457e15-8928-4418-9b27-30bd46b1ae30'],
			[$expected, '70457E15-8928-4418-9B27-30BD46B1AE30'],
			[$expected, '70457e15892844189b2730bd46b1ae30'],
			[$expected, '70457E15892844189B2730BD46B1AE30'],
			[$expected, '{70457e15-8928-4418-9b27-30bd46b1ae30}'],
			[$expected, '{70457E15-8928-4418-9B27-30BD46B1AE30}'],
			[$expected, '{70457e15892844189b2730bd46b1ae30}'],
			[$expected, '{70457E15892844189B2730BD46B1AE30}'],
		];
	}

	#[DataProvider('provider_adjustGuid')]
	public function test_adjustGuid(string $expected, string $value)
	{
		$actual = Uuid::adjustGuid($value);
		$this->assertSame($expected, $actual);
	}

	public function test_adjustGuid_error_length()
	{
		$this->expectException(ArgumentException::class);
		Uuid::adjustGuid('');
		$this->fail();
	}

	public function test_adjustGuid_error_guid()
	{
		$this->expectException(ArgumentException::class);
		Uuid::adjustGuid('G0457E15892844189B2730BD46B1AE30');
		$this->fail();
	}
}
