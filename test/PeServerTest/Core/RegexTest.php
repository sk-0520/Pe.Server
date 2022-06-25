<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Regex;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\RegexException;

class RegexTest extends TestClass
{
	public function test_isMatch()
	{
		$tests = [
			new Data(true, 'abc', '/a/')
		];
		foreach ($tests as $test) {
			$actual = Regex::isMatch(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}

	public function test_isMatch_throw()
	{
		$this->expectException(RegexException::class);
		Regex::isMatch('abcABC', '/(/');
		$this->fail();
	}

	public function test_matches()
	{
		$actual1 = Regex::matches('abc123XYZ', '/([a-z]+)/');
		$this->assertEquals('abc', $actual1[1]);

		$actual2 = Regex::matches('abc123XYZ', '/(?<NUM>\d+)/');
		$this->assertEquals('123', $actual2['NUM']);

		$actual3 = Regex::matches('abc123XYZ', '/(.)(.)(.)/');
		$this->assertEquals('a', $actual3[1]);
		$this->assertEquals('b', $actual3[2]);
		$this->assertEquals('c', $actual3[3]);
		$this->assertEquals('1', $actual3[4]);
		$this->assertEquals('2', $actual3[5]);
		$this->assertEquals('3', $actual3[6]);
		$this->assertEquals('X', $actual3[7]);
		$this->assertEquals('Y', $actual3[8]);
		$this->assertEquals('Z', $actual3[9]);

		$actual4 = Regex::matches('1234', '/(.)(?<NAME>.)/');
		$this->assertEquals('1', $actual4[1]);
		$this->assertEquals('2', $actual4[2]);
		$this->assertEquals('2', $actual4['NAME']);
		$this->assertEquals('3', $actual4[3]);
		$this->assertEquals('4', $actual4[4]);
	}

	public function test_matches_throw()
	{
		$this->expectException(RegexException::class);
		Regex::matches('abcABC', '/(/');
		$this->fail();
	}

	public function test_replace()
	{
		$tests = [
			new Data('XbcABC', 'abcABC', '/a/', 'X'),
			new Data('XbcXBC', 'abcABC', '/a/i', 'X'),
			new Data('-a-bcABC', 'abcABC', '/(a)/', '-$1-'),
			new Data('-a-bc-A-BC', 'abcABC', '/(a)/i', '-$1-'),
			new Data('🥚🐣🐥🐓🔪🍗', '🥚🐣🐥🐓🍗', '/(🐓)/', '$1🔪'),
			new Data('XbcABC', 'abcABC', '/a/i', 'X', 1),
		];
		foreach ($tests as $test) {
			$actual = Regex::replace(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_replace_throw()
	{
		$this->expectException(ArgumentException::class);
		Regex::replace('abcABC', '/a/i', 'X', 0);
		$this->fail();

		$this->expectException(RegexException::class);
		Regex::replace('abcABC', '/(/', 'X');
		$this->fail();
	}

	public function test_replaceCallback_test()
	{
		$tests = [
			new Data('[a]bcABC', 'abcABC', '/a/', fn ($m) => '[' . $m[0] . ']'),
			new Data('[a]bc[A]BC', 'abcABC', '/a/i', fn ($m) => '[' . $m[0] . ']'),
			new Data('[a]bcABC', 'abcABC', '/(?<NAME>a)/', fn ($m) => '[' . $m['NAME'] . ']'),
		];
		foreach ($tests as $test) {
			$actual = Regex::replaceCallback(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
}
