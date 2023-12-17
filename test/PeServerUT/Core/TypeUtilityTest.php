<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\TypeUtility;
use PeServer\Core\Throws\ParseException;

class TypeUtilityTest extends TestClass
{
	public function test_parseInteger()
	{
		$tests = [
			new Data(0, '0'),
			new Data(-1, '-1'),
			new Data(+1, '+1'),
			new Data(123, '123 '),
			new Data(456, ' 456'),
			new Data(789, '  789 '),
		];
		foreach ($tests as $test) {
			$actual = TypeUtility::parseInteger(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_parseInteger_throw()
	{
		$tests = [
			'',
			'1 1',
			'1+1',
			'1-1',
			'--1',
			'++1',
		];
		foreach ($tests as $test) {
			try {
				TypeUtility::parseInteger($test);
				$this->fail();
			} catch (ParseException) {
				$this->success();
			}
		}
	}

	public function test_tryParseInteger()
	{
		$result1 = TypeUtility::tryParseInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertSame(123, $actual1);

		$result2 = TypeUtility::tryParseInteger("abc", $actual2);
		$this->assertFalse($result2);
	}

	public function test_parseUInteger()
	{
		$tests = [
			new Data(0, '0'),
			//new Data(-1, '-1'),
			new Data(+1, '+1'),
			new Data(123, '123 '),
			new Data(456, ' 456'),
			new Data(789, '  789 '),
		];
		foreach ($tests as $test) {
			$actual = TypeUtility::parseUInteger(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_parseUInteger_throw()
	{
		$tests = [
			'',
			'1 1',
			'1+1',
			'1-1',
			'--1',
			'++1',
			'-1',
		];
		foreach ($tests as $test) {
			try {
				TypeUtility::parseUInteger($test);
				$this->fail();
			} catch (ParseException) {
				$this->success();
			}
		}
	}

	public function test_tryParseUInteger()
	{
		$result1 = TypeUtility::tryParseUInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertSame(123, $actual1);

		$result2 = TypeUtility::tryParseUInteger("-123", $actual2);
		$this->assertFalse($result2);
	}

	public function test_parsePositiveInteger()
	{
		$tests = [
			new Data(+1, '+1'),
			new Data(123, '123 '),
			new Data(456, ' 456'),
			new Data(789, '  789 '),
		];
		foreach ($tests as $test) {
			$actual = TypeUtility::parsePositiveInteger(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_parsePositiveInteger_throw()
	{
		$tests = [
			'',
			'1 1',
			'1+1',
			'1-1',
			'--1',
			'++1',
			'-1',
			'0',
		];
		foreach ($tests as $test) {
			try {
				TypeUtility::parsePositiveInteger($test);
				$this->fail();
			} catch (ParseException) {
				$this->success();
			}
		}
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

	public function test_parseBoolean()
	{
		$tests = [
			new Data(true, '1'),
			new Data(true, 'true'),
			new Data(true, 'TRUE'),
			new Data(true, 'on'),
			new Data(true, 'ok'),
			new Data(true, true),
			new Data(false, false),
			new Data(false, 'abc'),
			new Data(false, []),
			new Data(true, [0]),
		];
		foreach ($tests as $test) {
			$actual = TypeUtility::parseBoolean(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getType()
	{
		$tests = [
			new Data(TypeUtility::TYPE_INTEGER, 1),
			new Data(TypeUtility::TYPE_DOUBLE, 1.0),
			new Data(TypeUtility::TYPE_STRING, ''),
			new Data(TypeUtility::TYPE_NULL, null),
			new Data(TypeUtility::TYPE_ARRAY, []),
			new Data(TypeUtility::TYPE_ARRAY, [1, 2, 3]),
			new Data(TypeUtility::TYPE_ARRAY, ['A' => 'B']),
			new Data(self::class, $this),
		];
		foreach ($tests as $test) {
			$actual = TypeUtility::getType(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
