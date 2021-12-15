<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\Numeric;
use \PeServer\Core\Throws\ParseException;

class NumericTest extends TestClass
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
			$actual = Numeric::parseInteger(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_parseInteger_throw()
	{
		$this->expectException(ParseException::class);
		$tests = [
			'',
			'1 1',
			'1+1',
			'1-1',
			'--1',
			'++1',
		];
		foreach ($tests as $test) {
			Numeric::parseInteger($test);
		}
	}

	public function test_tryParseInteger()
	{
		$result1 = Numeric::tryParseInteger("123", $actual1);
		$this->assertTrue($result1);
		$this->assertEquals(123, $actual1);

		$result2 = Numeric::tryParseInteger("abc", $actual2);
		$this->assertFalse($result2);
	}
}
