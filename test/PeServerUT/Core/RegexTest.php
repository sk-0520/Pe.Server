<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Encoding;
use PeServer\Core\Regex;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\RegexDelimiterException;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\Throws\RegexPatternException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class RegexTest extends TestClass
{
	public static function provider_normalizePattern_default()
	{
		return [
			[true, 'abcde', '/a/'],
			[true, 'abcde', '(b)'],
			[true, 'abcde', '{c}'],
			[true, 'abcde', '[c]'],
			[true, 'abcde', '<d>'],
			[true, 'abcde', '/e/i'],
			[false, 'abcde', '/E/'],
			[true, 'abcde', '/E/i'],
			[true, 'abcde', '/E/i'],
		];
	}

	#[DataProvider('provider_normalizePattern_default')]
	public function test_normalizePattern_default(bool $expected, string $input, string $pattern)
	{
		$regex = new Regex();
		$actual = $regex->isMatch($input, $pattern);
		$this->assertSame($expected, $actual);
	}

	public static function provider_normalizePattern_ascii()
	{
		return [
			[true, 'abcde', '/a/'],
			[true, 'abcde', '(b)'],
			[true, 'abcde', '{c}'],
			[true, 'abcde', '[c]'],
			[true, 'abcde', '<d>'],
			[true, 'abcde', '/e/i'],
			[false, 'abcde', '/E/'],
			[true, 'abcde', '/E/i'],
			[true, 'abcde', '/E/i'],
		];
	}

	#[DataProvider('provider_normalizePattern_ascii')]
	public function test_normalizePattern_ascii(bool $expected, string $input, string $pattern)
	{
		$regex = new Regex(Encoding::getAscii());
		$actual = $regex->isMatch($input, $pattern);
		$this->assertSame($expected, $actual);
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

	public static function provider_replace()
	{
		return [
			['XbcABC', 'abcABC', '/a/', 'X'],
			['XbcXBC', 'abcABC', '/a/i', 'X'],
			['-a-bcABC', 'abcABC', '/(a)/', '-$1-'],
			['-a-bc-A-BC', 'abcABC', '/(a)/i', '-$1-'],
			['🥚🐣🐥🐓🔪🍗', '🥚🐣🐥🐓🍗', '/(🐓)/', '$1🔪'],
			['XbcABC', 'abcABC', '/a/i', 'X', 1],
		];
	}

	#[DataProvider('provider_replace')]
	public function test_replace(string $expected, string $source, string $pattern, string $replacement, int $limit = Regex::UNLIMITED)
	{
		$regex = new Regex();
		$actual = $regex->replace($source, $pattern, $replacement, $limit);
		$this->assertSame($expected, $actual);
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

	public static function provider_replaceCallback_test()
	{
		return [
			['[a]bcABC', 'abcABC', '/a/', fn ($m) => '[' . $m[0] . ']'],
			['[a]bc[A]BC', 'abcABC', '/a/i', fn ($m) => '[' . $m[0] . ']'],
			['[a]bcABC', 'abcABC', '/(?<NAME>a)/', fn ($m) => '[' . $m['NAME'] . ']'],
		];
	}

	#[DataProvider('provider_replaceCallback_test')]
	public function test_replaceCallback_test(string $expected, string $source, string $pattern, callable $replacement, int $limit = Regex::UNLIMITED)
	{
		$regex = new Regex();
		$actual = $regex->replaceCallback($source, $pattern, $replacement, $limit);
		$this->assertSame($expected, $actual);
	}

	public static function provider_split()
	{
		return [
			[['a', 'b', 'c'], 'a,b,c', '/,/'],
			[['1', '2', '3'], '1a2bc3', '/[a-z]+/'],
		];
	}

	#[DataProvider('provider_split')]
	public function test_split(array $expected, string $source, string $pattern)
	{
		$regex = new Regex();
		$actual = $regex->split($source, $pattern);
		$this->assertSame($expected, $actual);
	}
}
