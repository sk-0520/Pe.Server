<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\TypeUtility;
use PeServer\Core\Throws\ParseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use stdClass;

class TypeUtilityTest extends TestClass
{
	public static function provider_parseInteger()
	{
		return [
			[0, '0'],
			[-1, '-1'],
			[+1, '+1'],
			[123, '123 '],
			[456, ' 456'],
			[789, '  789 '],
		];
	}

	#[DataProvider('provider_parseInteger')]
	public function test_parseInteger(int $expected, string $input)
	{
		$actual = TypeUtility::parseInteger($input);
		$this->assertSame($expected, $actual);
	}

	public static function provider_parseInteger_throw()
	{
		return [
			[''],
			['1 1'],
			['1+1'],
			['1-1'],
			['--1'],
			['++1'],
		];
	}

	#[DataProvider('provider_parseInteger_throw')]
	public function test_parseInteger_throw(string $input)
	{
		$this->expectException(ParseException::class);
		TypeUtility::parseInteger($input);
		$this->fail();
	}

	public function test_tryParseInteger()
	{
		$result1 = TypeUtility::tryParseInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertSame(123, $actual1);

		$result2 = TypeUtility::tryParseInteger("abc", $actual2);
		$this->assertFalse($result2);
	}

	public static function provider_parseUInteger()
	{
		return [
			[0, '0'],
			//[-1, '-1'],
			[+1, '+1'],
			[123, '123 '],
			[456, ' 456'],
			[789, '  789 '],
		];
	}

	#[DataProvider('provider_parseUInteger')]
	public function test_parseUInteger(int $expected, string $input)
	{
		$actual = TypeUtility::parseUInteger($input);
		$this->assertSame($expected, $actual);
	}

	public static function provider_parseUInteger_throw()
	{
		return [
			[''],
			['1 1'],
			['1+1'],
			['1-1'],
			['--1'],
			['++1'],
			['-1'],
		];
	}

	#[DataProvider('provider_parseUInteger_throw')]
	public function test_parseUInteger_throw(string $input)
	{
		$this->expectException(ParseException::class);
		TypeUtility::parseUInteger($input);
		$this->fail();
	}

	public function test_tryParseUInteger()
	{
		$result1 = TypeUtility::tryParseUInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertSame(123, $actual1);

		$result2 = TypeUtility::tryParseUInteger("-123", $actual2);
		$this->assertFalse($result2);
	}

	public static function provider_parsePositiveInteger()
	{
		return [
			[+1, '+1'],
			[123, '123 '],
			[456, ' 456'],
			[789, '  789 '],
		];
	}

	#[DataProvider('provider_parsePositiveInteger')]
	public function test_parsePositiveInteger(int $expected, string $input)
	{
		$actual = TypeUtility::parsePositiveInteger($input);
		$this->assertSame($expected, $actual);
	}

	public static function provider_parsePositiveInteger_throw()
	{
		return [
			[''],
			['1 1'],
			['1+1'],
			['1-1'],
			['--1'],
			['++1'],
			['-1'],
			['0'],
		];
	}

	#[DataProvider('provider_parsePositiveInteger_throw')]
	public function test_parsePositiveInteger_throw(string $input)
	{
		$this->expectException(ParseException::class);
		TypeUtility::parsePositiveInteger($input);
		$this->fail();
	}

	public function test_tryParsePositiveInteger()
	{
		$result1 = TypeUtility::tryParsePositiveInteger("1", $actual1);
		$this->assertTrue($result1);
		$this->assertSame(1, $actual1);

		$result2 = TypeUtility::tryParsePositiveInteger("0", $actual2);
		$this->assertFalse($result2);

		$result3 = TypeUtility::tryParsePositiveInteger("-1", $actual3);
		$this->assertFalse($result3);
	}

	public static function provider_parseBoolean()
	{
		return [
			[true, '1'],
			[true, 'true'],
			[true, 'TRUE'],
			[true, 'on'],
			[true, 'ok'],
			[true, true],
			[false, false],
			[false, 'abc'],
			[false, []],
			[true, [0]],
		];
	}

	#[DataProvider('provider_parseBoolean')]
	public function test_parseBoolean(bool $expected, mixed $input)
	{
		$actual = TypeUtility::parseBoolean($input);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getType()
	{
		return [
			[TypeUtility::TYPE_INTEGER, 1],
			[TypeUtility::TYPE_DOUBLE, 1.0],
			[TypeUtility::TYPE_STRING, ''],
			[TypeUtility::TYPE_NULL, null],
			[TypeUtility::TYPE_ARRAY, []],
			[TypeUtility::TYPE_ARRAY, [1, 2, 3]],
			[TypeUtility::TYPE_ARRAY, ['A' => 'B']],
			[stdClass::class, new stdClass()],
		];
	}

	#[DataProvider('provider_getType')]
	public function test_getType(string $expected, mixed $input)
	{
		$actual = TypeUtility::getType($input);
		$this->assertSame($expected, $actual);
	}

	public function test_getSimpleClassName_namespace()
	{
		$actual = TypeUtility::getSimpleClassName($this);
		$this->assertSame("TypeUtilityTest", $actual);
	}

	public function test_getSimpleClassName_flat()
	{
		$actual = TypeUtility::getSimpleClassName(new stdClass());
		$this->assertSame("stdClass", $actual);
	}
}
