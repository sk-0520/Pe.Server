<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Encoding;
use PeServer\Core\Regex;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\RegexDelimiterException;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\Throws\RegexPatternException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class RegexTest extends TestClass
{
	public function test_normalizePattern_default()
	{
		$tests = [
			new Data(true, 'abcde', '/a/'),
			new Data(true, 'abcde', '(b)'),
			new Data(true, 'abcde', '{c}'),
			new Data(true, 'abcde', '[c]'),
			new Data(true, 'abcde', '<d>'),
			new Data(true, 'abcde', '/e/i'),
			new Data(false, 'abcde', '/E/'),
			new Data(true, 'abcde', '/E/i'),
			new Data(true, 'abcde', '/E/i'),
		];
		foreach ($tests as $test) {
			$regex = new Regex();
			$actual = $regex->isMatch(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_normalizePattern_ascii()
	{
		$tests = [
			new Data(true, 'abcde', '/a/'),
			new Data(true, 'abcde', '(b)'),
			new Data(true, 'abcde', '{c}'),
			new Data(true, 'abcde', '[c]'),
			new Data(true, 'abcde', '<d>'),
			new Data(true, 'abcde', '/e/i'),
			new Data(false, 'abcde', '/E/'),
			new Data(true, 'abcde', '/E/i'),
			new Data(true, 'abcde', '/E/i'),
		];
		foreach ($tests as $test) {
			$regex = new Regex(Encoding::getAscii());
			$actual = $regex->isMatch(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_normalizePattern_length_throw()
	{
		$this->expectException(RegexPatternException::class);
		$regex = new Regex();
		$regex->isMatch('asd', '//');
		$this->fail();
	}

	public function test_normalizePattern_close_throw()
	{
		$this->expectException(RegexDelimiterException::class);
		$regex = new Regex();
		$regex->isMatch('asd', '/a@');
		$this->fail();
	}

	public function test_isMatch_throw()
	{
		$this->expectException(RegexException::class);
		$regex = new Regex();
		$regex->isMatch('abcABC', '/(/');
		$this->fail();
	}

	public function test_matches()
	{
		$regex = new Regex();
		$actual0 = $regex->matches('abc123XYZ', '/(aaaa)/');
		$this->assertSame(0, count($actual0));

		$actual1 = $regex->matches('abc123XYZ', '/([a-z]+)/');
		$this->assertSame('abc', $actual1[1]);

		$actual2 = $regex->matches('abc123XYZ', '/(?<NUM>\d+)/');
		$this->assertSame('123', $actual2['NUM']);

		$actual3 = $regex->matches('abc123XYZ', '/(.)(.)(.)/');
		$this->assertSame('a', $actual3[1]);
		$this->assertSame('b', $actual3[2]);
		$this->assertSame('c', $actual3[3]);
		$this->assertSame('1', $actual3[4]);
		$this->assertSame('2', $actual3[5]);
		$this->assertSame('3', $actual3[6]);
		$this->assertSame('X', $actual3[7]);
		$this->assertSame('Y', $actual3[8]);
		$this->assertSame('Z', $actual3[9]);

		$actual4 = $regex->matches('1234', '/(.)(?<NAME>.)/');
		$this->assertSame('1', $actual4[1]);
		$this->assertSame('2', $actual4[2]);
		$this->assertSame('2', $actual4['NAME']);
		$this->assertSame('3', $actual4[3]);
		$this->assertSame('4', $actual4[4]);
	}

	public function test_matches_throw()
	{
		$this->expectException(RegexException::class);
		$regex = new Regex();
		$regex->matches('abcABC', '/(/');
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
			$regex = new Regex();
			$actual = $regex->replace(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_replace_throw1()
	{
		$regex = new Regex();
		$this->expectException(ArgumentException::class);
		$regex->replace('abcABC', '/a/i', 'X', 0);
		$this->fail();
	}

	public function test_replace_throw2()
	{
		$regex = new Regex();
		$this->expectException(RegexException::class);
		$regex->replace('abcABC', '/(/', 'X');
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
			$regex = new Regex();
			$actual = $regex->replaceCallback(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_split()
	{
		$tests = [
			new Data(['a', 'b', 'c'], 'a,b,c', '/,/'),
			new Data(['1', '2', '3'], '1a2bc3', '/[a-z]+/'),
		];
		foreach ($tests as $test) {
			$regex = new Regex();
			$actual = $regex->split(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
