<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\TypeConverter;
use PeServer\Core\Throws\ParseException;

class TypeConverterTest extends TestClass
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
			$actual = TypeConverter::parseInteger(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
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
				TypeConverter::parseInteger($test);
				$this->fail();
			} catch(ParseException) {
				$this->success();
			}
		}
	}

	public function test_tryParseInteger()
	{
		$result1 = TypeConverter::tryParseInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertEquals(123, $actual1);

		$result2 = TypeConverter::tryParseInteger("abc", $actual2);
		$this->assertFalse($result2);
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
			$actual = TypeConverter::parseBoolean(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}
}
