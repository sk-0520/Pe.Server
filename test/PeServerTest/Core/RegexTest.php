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
}
