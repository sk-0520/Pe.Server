<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;

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
			$this->assertBoolean($test->expected, $actual, $test->str()); //@php-ignore-line
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
			$this->assertBoolean($test->expected, $actual, $test->str());
		}
	}

	public function test_getLength()
	{
		$tests = [
			new Data(0, ''),
			new Data(1, 'a'),
			new Data(1, 'あ'),
			new Data(1, '☃'),
			new Data(1, '⛄'),
			new Data(1, '👭'),
			new Data(5, '🧑‍🤝‍🧑'),
			new Data(7, '👨‍👩‍👧‍👦'),

		];
		foreach ($tests as $test) {
			$actual = StringUtility::getLength(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	/*
	public function test_getCharacterLength()
	{
		$tests = [
			new Data(0, ''),
			new Data(1, 'a'),
			new Data(1, 'あ'),
			new Data(1, '☃'),
			new Data(1, '⛄'),
			new Data(1, '👭'),
			new Data(1, '🧑‍🤝‍🧑'),
			new Data(1, '👨‍👩‍👧‍👦'),

		];
		foreach ($tests as $test) {
			$actual = StringUtility::getCharacterLength(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}
	*/

	public function test_fromCodePoint()
	{
		$tests = [
			new Data('A', 65),
			new Data('AB', [65, 66]),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::fromCodePoint(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
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
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_formatNumber()
	{
		$tests = [
			new Data('123', 123),
			new Data('1,234', 1234),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::formatNumber(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_getPosition()
	{
		$tests = [
			new Data(0, 'abcあいう☃⛄', 'a'),
			new Data(3, 'abcあいう☃⛄', 'あ'),
			new Data(6, 'abcあいう☃⛄', '☃'),
			new Data(7, 'abcあいう☃⛄', '⛄'),
			new Data(-1, 'abcあいう☃⛄', '🐡'),

			new Data(3, 'abcあいう☃⛄', 'あ', 3),
			new Data(-1, 'abcあいう☃⛄', '☃', 7),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::getPosition(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
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

	public function test_substring(): void
	{
		$tests = [
			new Data('abc', 'abcxyz', 0, 3),
			new Data('xyz', 'abcxyz', 3, 3),
			new Data('xyz', 'abcxyz', 3),
			new Data('xy', 'abcxyz', -3, 2),
			new Data('xyz', 'abcxyz', -3),
			new Data('感☃🐎', 'あ感☃🐎', 1),
			new Data('🐎', 'あ感☃🐎', 3),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::substring(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_toLower()
	{
		$tests = [
			new Data('a', 'A'),
			new Data('a', 'a'),
			new Data('aａ', 'aＡ'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::toLower(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_toUpper()
	{
		$tests = [
			new Data('A', 'a'),
			new Data('A', 'A'),
			new Data('AＡ', 'aａ'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::toUpper(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_split()
	{
		$tests = [
			new Data(['a', 'b', 'c'], 'a,b,c', ','),
			new Data(['a', 'b', 'c'], 'a::b::c', '::'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::split(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_join()
	{
		$tests = [
			new Data('abc', '', ['a', 'b', 'c']),
			new Data('a,b,c', ',', ['a', 'b', 'c']),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::join(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_trim()
	{
		$tests = [
			new Data('a', 'a'),
			new Data('a', ' a'),
			new Data('a', 'a '),
			new Data('a', ' a '),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::trim(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}


	public function test_trimStart()
	{
		$tests = [
			new Data('a', 'a'),
			new Data('a', ' a'),
			new Data('a ', 'a '),
			new Data('a ', ' a '),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::trimStart(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_trimEnd()
	{
		$tests = [
			new Data('a', 'a'),
			new Data(' a', ' a'),
			new Data('a', 'a '),
			new Data(' a', ' a '),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::trimEnd(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_replace()
	{
		$tests = [
			new Data('Abcdef-Abcdef', 'abcdef-abcdef', 'a', 'A'),
			new Data('abcxyz-abcxyz', 'abcdef-abcdef', 'def', 'xyz'),
			new Data('🏇あｱ☃⛄', 'aあｱ☃⛄', 'a', '🏇'),
			new Data('a🏇ｱ☃⛄', 'aあｱ☃⛄', 'あ', '🏇'),
			new Data('aあ🏇☃⛄', 'aあｱ☃⛄', 'ｱ', '🏇'),
			new Data('aあｱ🏇⛄', 'aあｱ☃⛄', '☃', '🏇'),
			new Data('aあｱ☃🏇', 'aあｱ☃⛄', '⛄', '🏇'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::replace(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_replace_array()
	{
		$tests = [
			new Data('______', 'ABCABC', ['A', 'B', 'C'], '_'),
			new Data('___abc', 'ABCabc', ['A', 'B', 'C'], '_'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::replace(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_repeat()
	{
		$tests = [
			new Data('', 'a', 0),
			new Data('a', 'a', 1),
			new Data('aa', 'a', 2),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::repeat(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_repeat_exception()
	{
		$this->expectException(ArgumentException::class);
		StringUtility::repeat('', -1);
		$this->fail();
	}

	public function test_toCharacters()
	{
		$tests = [
			new Data([' '], ' '),
			new Data(['a', 'b', 'c'], 'abc'),
			new Data(['あ', 'い', 'う'], 'あいう'),
			new Data(['☃', '⛄', '𩸽', '🏇'], '☃⛄𩸽🏇'),
		];
		foreach ($tests as $test) {
			$actual = StringUtility::toCharacters(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_toCharacters_throw()
	{
		$this->expectException(ArgumentException::class);
		StringUtility::toCharacters('');
		$this->fail();
	}
}
