<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use \PeServerTest\Data;
use \PeServerTest\TestClass;
use \PeServer\Core\StringUtility;

class StringUtilityTest extends TestClass
{
	public function test_isNullOrEmpty()
	{
		$tests = [
			new Data(true, null),
			new Data(true, ''),
			new Data(false, ' '),
			new Data(false, '0'),
			new Data(false, 'abc'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::isNullOrEmpty(...$test->args);
			$this->assertBoolean($test->expected, $actual);
		}
	}

	public function test_isNullOrWhiteSpace()
	{
		$tests = [
			new Data(true, null),
			new Data(true, ''),
			new Data(true, ' '),
			new Data(false, '0'),
			new Data(false, 'abc'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::isNullOrWhiteSpace(...$test->args);
			$this->assertBoolean($test->expected, $actual);
		}
	}

	public function test_replaceMap()
	{
		$tests = [
			new Data('abc', '{A}{B}{C}', ['A' => 'a', 'B' => 'b', 'C' => 'c',]),
			new Data('', '{x}{y}{z}', ['A' => 'a', 'B' => 'b', 'C' => 'c',]),
			new Data('a!?', '{A}{a}{!}', ['A' => 'a', 'a' => '!', '!' => '?',]),
			new Data('(a)[a]<a>', '({A})[{A}]<{A}>', ['A' => 'a',]),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::replaceMap(...$test->args);
			$this->assertEquals($test->expected, $actual);
		}
	}

	public function test_startsWith()
	{
		$tests = [
			new Data(true, 'abc', '', false),
			new Data(true, 'abc', 'a', false),
			new Data(true, 'abc', 'ab', false),
			new Data(true, 'abc', 'abc', false),
			new Data(false, 'abc', 'abcd', false),

			new Data(false, 'abc', 'A', false),
			new Data(false, 'abc', 'AB', false),
			new Data(false, 'abc', 'ABC', false),
			new Data(false, 'abc', 'ABCD', false),

			new Data(true, 'abc', '', true),
			new Data(true, 'abc', 'A', true),
			new Data(true, 'abc', 'AB', true),
			new Data(true, 'abc', 'ABC', true),
			new Data(false, 'abc', 'ABCD', true),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::startsWith(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}

	public function test_endsWith()
	{
		$tests = [
			new Data(true, 'abc', '', false),
			new Data(true, 'abc', 'c', false),
			new Data(true, 'abc', 'bc', false),
			new Data(true, 'abc', 'abc', false),
			new Data(false, 'abc', 'abcd', false),

			new Data(false, 'abc', 'C', false),
			new Data(false, 'abc', 'BC', false),
			new Data(false, 'abc', 'ABC', false),
			new Data(false, 'abc', 'ABCD', false),

			new Data(true, 'abc', '', true),
			new Data(true, 'abc', 'C', true),
			new Data(true, 'abc', 'BC', true),
			new Data(true, 'abc', 'ABC', true),
			new Data(false, 'abc', 'ABCD', true),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::endsWith(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}

	public function test_contains()
	{
		$tests = [
			new Data(true, 'abc', '', false),
			new Data(true, 'abc', 'b', false),
			new Data(true, 'abc', 'ab', false),
			new Data(true, 'abc', 'bc', false),
			new Data(true, 'abc', 'abc', false),
			new Data(false, 'abc', 'x', false),
			new Data(false, 'abc', 'abcd', false),

			new Data(true, 'abc', '', false),
			new Data(false, 'abc', 'B', false),
			new Data(false, 'abc', 'AB', false),
			new Data(false, 'abc', 'BC', false),
			new Data(false, 'abc', 'ABC', false),
			new Data(false, 'abc', 'X', false),
			new Data(false, 'abc', 'ABCD', false),

			new Data(true, 'abc', '', true),
			new Data(true, 'abc', 'B', true),
			new Data(true, 'abc', 'AB', true),
			new Data(true, 'abc', 'BC', true),
			new Data(true, 'abc', 'ABC', true),
			new Data(false, 'abc', 'X', true),
			new Data(false, 'abc', 'ABCD', true),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::contains(...$test->args);
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}
}
