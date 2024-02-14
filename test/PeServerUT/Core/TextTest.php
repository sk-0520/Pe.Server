<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

class TextTest extends TestClass
{
	public static function provider_isNullOrEmpty()
	{
		return [
			[true, null],
			[true, ''],
			[false, ' '],
			[false, '0'],
			[false, 'abc'],
		];
	}

	#[DataProvider('provider_isNullOrEmpty')]
	public function test_isNullOrEmpty(bool $expected, ?string $s)
	{
		$actual = Text::isNullOrEmpty($s);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isNullOrWhiteSpace()
	{
		return [
			[true, null],
			[true, ''],
			[true, ' '],
			[true, "\r"],
			[true, "\n"],
			[true, "\t"],
			//全角 [true, "　"],
			[false, '0'],
			[false, 'abc'],
		];
	}

	#[DataProvider('provider_isNullOrWhiteSpace')]
	public function test_isNullOrWhiteSpace(bool $expected, ?string $s)
	{
		$actual = Text::isNullOrWhiteSpace($s);
		$this->assertSame($expected, $actual);
	}

	public static function provider_requireNotNullOrEmpty()
	{
		return [
			['A', 'A', 'B'],
			['B', '', 'B'],
			[' ', ' ', 'B'],
			['B', null, 'B'],
		];
	}

	#[DataProvider('provider_requireNotNullOrEmpty')]
	public function test_requireNotNullOrEmpty(string $expected, ?string $s, string $fallback)
	{
		$actual = Text::requireNotNullOrEmpty($s, $fallback);
		$this->assertSame($expected, $actual);
	}

	public static function provider_requireNotNullOrWhiteSpace()
	{
		return [
			['A', 'A', 'B'],
			['B', '', 'B'],
			['B', ' ', 'B'],
			['B', null, 'B'],
		];
	}

	#[DataProvider('provider_requireNotNullOrWhiteSpace')]
	public function test_requireNotNullOrWhiteSpace(string $expected, ?string $s, string $fallback)
	{
		$actual = Text::requireNotNullOrWhiteSpace($s, $fallback);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getLength()
	{
		return [
			[0, ''],
			[1, 'a'],
			[1, 'あ'],
			[1, '☃'],
			[1, "\0"],
			[2, "\0\0"],
			[3, "A\0\0"],
			[1, '⛄'],
			[1, '👭'],
			[5, '🧑‍🤝‍🧑'],
			[7, '👨‍👩‍👧‍👦'],
		];
	}

	#[DataProvider('provider_getLength')]
	public function test_getLength(int $expected, string $value)
	{
		$actual = Text::getLength($value);
		$this->assertSame($expected, $actual);
	}

	/*
	public static function provider_getCharacterLength()
	{
		return [
			[0, ''],
			[1, 'a'],
			[1, 'あ'],
			[1, '☃'],
			[1, '⛄'],
			[1, '👭'],
			[1, '🧑‍🤝‍🧑'],
			[1, '👨‍👩‍👧‍👦'],
		];
	}

	#[DataProvider('provider_getCharacterLength')]
	public function test_getCharacterLength(int $expected, string $value)
	{
		$actual = Text::getCharacterLength($value);
		$this->assertSame($expected, $actual);
	}
	*/

	public static function provider_fromCodePoint()
	{
		return [
			['A', 65],
			['AB', [65, 66]],
		];
	}

	#[DataProvider('provider_fromCodePoint')]
	public function test_fromCodePoint(string $expected, int|array $value)
	{
		$actual = Text::fromCodePoint($value);
		$this->assertSame($expected, $actual);
	}

	public function test_fromCodePoint_array_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::fromCodePoint(['a']);
		$this->fail();
	}


	public function test_fromCodePoint_char_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::fromCodePoint(-1);
		$this->fail();
	}


	public static function provider_replaceMap()
	{
		return [
			['abc', '{A}{B}{C}', ['A' => 'a', 'B' => 'b', 'C' => 'c',]],
			['', '{x}{y}{z}', ['A' => 'a', 'B' => 'b', 'C' => 'c',]],
			['a!?', '{A}{a}{!}', ['A' => 'a', 'a' => '!', '!' => '?',]],
			['(a)[a]<a>', '({A})[{A}]<{A}>', ['A' => 'a',]],
		];
	}

	#[DataProvider('provider_replaceMap')]
	public function test_replaceMap(string $expected, string $source, array $map, string $head = '{', string $tail = '}')
	{
		$actual = Text::replaceMap($source, $map, $head, $tail);
		$this->assertSame($expected, $actual);
	}

	public static function provider_formatNumber()
	{
		return [
			['123', 123],
			['1,234', 1234],
		];
	}

	#[DataProvider('provider_formatNumber')]
	public function test_formatNumber(string $expected, int|float $number, int $decimals = 0, ?string $decimalSeparator = '.', ?string $thousandsSeparator = ',')
	{
		$actual = Text::formatNumber($number, $decimals, $decimalSeparator, $thousandsSeparator);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getPosition()
	{
		return [
			[0, 'abcあいう☃⛄', 'a'],
			[3, 'abcあいう☃⛄', 'あ'],
			[6, 'abcあいう☃⛄', '☃'],
			[7, 'abcあいう☃⛄', '⛄'],
			[-1, 'abcあいう☃⛄', '🐡'],

			[3, 'abcあいう☃⛄', 'あ', 3],
			[-1, 'abcあいう☃⛄', '☃', 7],
		];
	}

	#[DataProvider('provider_getPosition')]
	public function test_getPosition(int $expected, string $haystack, string $needle, int $offset = 0)
	{
		$actual = Text::getPosition($haystack, $needle, $offset);
		$this->assertSame($expected, $actual);
	}

	public function test_getPosition_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::getPosition('abc', 'b', -1);
		$this->fail();
	}

	public function test_getLastPosition_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::getLastPosition('abc', 'b', -1);
		$this->fail();
	}

	public static function provider_startsWith()
	{
		return [
			[true, 'abc', '', false],
			[true, 'abc', 'a', false],
			[true, 'abc', 'ab', false],
			[true, 'abc', 'abc', false],
			[false, 'abc', 'abcd', false],

			[false, 'abc', 'A', false],
			[false, 'abc', 'AB', false],
			[false, 'abc', 'ABC', false],
			[false, 'abc', 'ABCD', false],

			[true, 'abc', '', true],
			[true, 'abc', 'A', true],
			[true, 'abc', 'AB', true],
			[true, 'abc', 'ABC', true],
			[false, 'abc', 'ABCD', true],
		];
	}

	#[DataProvider('provider_startsWith')]
	public function test_startsWith(bool $expected, string $haystack, string $needle, bool $ignoreCase)
	{
		$actual = Text::startsWith($haystack, $needle, $ignoreCase);
		$this->assertSame($expected, $actual);
	}

	public static function provider_endsWith()
	{
		return [
			[true, 'abc', '', false],
			[true, 'abc', 'c', false],
			[true, 'abc', 'bc', false],
			[true, 'abc', 'abc', false],
			[false, 'abc', 'abcd', false],

			[false, 'abc', 'C', false],
			[false, 'abc', 'BC', false],
			[false, 'abc', 'ABC', false],
			[false, 'abc', 'ABCD', false],

			[true, 'abc', '', true],
			[true, 'abc', 'C', true],
			[true, 'abc', 'BC', true],
			[true, 'abc', 'ABC', true],
			[false, 'abc', 'ABCD', true],
		];
	}

	#[DataProvider('provider_endsWith')]
	public function test_endsWith(bool $expected, string $haystack, string $needle, bool $ignoreCase)
	{
		$tests = [
			[true, 'abc', '', false],
			[true, 'abc', 'c', false],
			[true, 'abc', 'bc', false],
			[true, 'abc', 'abc', false],
			[false, 'abc', 'abcd', false],

			[false, 'abc', 'C', false],
			[false, 'abc', 'BC', false],
			[false, 'abc', 'ABC', false],
			[false, 'abc', 'ABCD', false],

			[true, 'abc', '', true],
			[true, 'abc', 'C', true],
			[true, 'abc', 'BC', true],
			[true, 'abc', 'ABC', true],
			[false, 'abc', 'ABCD', true],
		];
		foreach ($tests as $test) {
			$actual = Text::endsWith($haystack, $needle, $ignoreCase);
			$this->assertSame($expected, $actual);
		}
	}

	public static function provider_contains()
	{
		return [
			[true, 'abc', '', false],
			[true, 'abc', 'b', false],
			[true, 'abc', 'ab', false],
			[true, 'abc', 'bc', false],
			[true, 'abc', 'abc', false],
			[false, 'abc', 'x', false],
			[false, 'abc', 'abcd', false],

			[true, 'abc', '', false],
			[false, 'abc', 'B', false],
			[false, 'abc', 'AB', false],
			[false, 'abc', 'BC', false],
			[false, 'abc', 'ABC', false],
			[false, 'abc', 'X', false],
			[false, 'abc', 'ABCD', false],

			[true, 'abc', '', true],
			[true, 'abc', 'B', true],
			[true, 'abc', 'AB', true],
			[true, 'abc', 'BC', true],
			[true, 'abc', 'ABC', true],
			[false, 'abc', 'X', true],
			[false, 'abc', 'ABCD', true],
		];
	}

	#[DataProvider('provider_contains')]
	public function test_contains(bool $expected, string $haystack, string $needle, bool $ignoreCase)
	{
		$actual = Text::contains($haystack, $needle, $ignoreCase);
		$this->assertSame($expected, $actual);
	}

	public static function provider_substring()
	{
		return [
			['abc', 'abcxyz', 0, 3],
			['xyz', 'abcxyz', 3, 3],
			['xyz', 'abcxyz', 3],
			['xy', 'abcxyz', -3, 2],
			['xyz', 'abcxyz', -3],
			['感☃🐎', 'あ感☃🐎', 1],
			['🐎', 'あ感☃🐎', 3],
		];
	}

	#[DataProvider('provider_substring')]
	public function test_substring(string $expected, string $value, int $offset, int $length = -1): void
	{
		$actual = Text::substring($value, $offset, $length);
		$this->assertSame($expected, $actual);
	}

	public static function provider_toLower()
	{
		return [
			['a', 'A'],
			['a', 'a'],
			['aａ', 'aＡ'],
		];
	}

	#[DataProvider('provider_toLower')]
	public function test_toLower(string $expected, string $value)
	{
		$actual = Text::toLower($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_toUpper()
	{
		return [
			['A', 'a'],
			['A', 'A'],
			['AＡ', 'aａ'],
		];
	}

	#[DataProvider('provider_toUpper')]
	public function test_toUpper(string $expected, string $value)
	{
		$actual = Text::toUpper($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_split()
	{
		return [
			[['a', 'b', 'c'], 'a,b,c', ','],
			[['a', 'b', 'c'], 'a::b::c', '::'],
			[['a::b::c'], 'a::b::c', '::', 0],
			[['a::b::c'], 'a::b::c', '::', 1],
			[['a', 'b::c'], 'a::b::c', '::', 2],
			[['a', 'b', 'c'], 'a::b::c', '::', 3],
			[['a', 'b', 'c'], 'a::b::c', '::', 4],
			[[''], '', ','],
			[['a,b,c'], 'a,b,c', ':'],
		];
	}

	#[DataProvider('provider_split')]
	public function test_split(array $expected, string $value, string $separator, int $limit = PHP_INT_MAX)
	{
		$actual = Text::split($value, $separator, $limit);
		$this->assertSame($expected, $actual);
	}

	public function test_split_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::split('abc', '');
		$this->fail();
	}


	public static function provider_splitLines()
	{
		return [
			[['a', 'b', 'c'], "a\nb\nc"],
			[['a', 'b', 'c'], "a\rb\rc"],
			[['a', 'b', 'c'], "a\r\nb\r\nc"],
			[['a', 'b', '', 'c', ''], "a\rb\n\rc\r\n"],
		];
	}

	#[DataProvider('provider_splitLines')]
	public function test_splitLines(array $expected, string $value)
	{
		$actual = Text::splitLines($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_join()
	{
		return [
			['abc', '', ['a', 'b', 'c']],
			['a,b,c', ',', ['a', 'b', 'c']],
		];
	}

	#[DataProvider('provider_join')]
	public function test_join(string $expected, string $separator, array $values)
	{
		$actual = Text::join($separator, $values);
		$this->assertSame($expected, $actual);
	}

	public static function provider_trim()
	{
		return [
			['a', 'a'],
			['a', ' a'],
			['a', 'a '],
			['a', ' a '],
			['あ', '　あ　'],
			['あ', 'あ　'],
			['あああ', 'あああ'],
			['⚽', '🥅⚽🥅', '🥅'],
			['かきくけこ', 'あいうえおかきくけこあいうえお', 'あ..お'],
		];
	}

	#[DataProvider('provider_trim')]
	public function test_trim(string $expected, string $value, string $characters = Text::TRIM_CHARACTERS)
	{
		$actual = Text::trim($value, $characters);
		$this->assertSame($expected, $actual);
	}

	public static function provider_trimStart()
	{
		return [
			['a', 'a'],
			['a', ' a'],
			['a ', 'a '],
			['a ', ' a '],
			['あ　', '　あ　'],
			['⚽🥅', '🥅⚽🥅', '🥅'],
		];
	}

	#[DataProvider('provider_trimStart')]
	public function test_trimStart(string $expected, string $value, string $characters = Text::TRIM_CHARACTERS)
	{
		$actual = Text::trimStart($value, $characters);
		$this->assertSame($expected, $actual);
	}

	public static function provider_trimEnd()
	{
		return [
			['a', 'a'],
			[' a', ' a'],
			['a', 'a '],
			[' a', ' a '],
			['　あ', '　あ　'],
			['🥅⚽', '🥅⚽🥅', '🥅'],
		];
	}

	#[DataProvider('provider_trimEnd')]
	public function test_trimEnd(string $expected, string $value, string $characters = Text::TRIM_CHARACTERS)
	{
		$actual = Text::trimEnd($value, $characters);
		$this->assertSame($expected, $actual);
	}

	public static function provider_replace()
	{
		return [
			['Abcdef-Abcdef', 'abcdef-abcdef', 'a', 'A'],
			['abcxyz-abcxyz', 'abcdef-abcdef', 'def', 'xyz'],
			['🏇あｱ☃⛄', 'aあｱ☃⛄', 'a', '🏇'],
			['a🏇ｱ☃⛄', 'aあｱ☃⛄', 'あ', '🏇'],
			['aあ🏇☃⛄', 'aあｱ☃⛄', 'ｱ', '🏇'],
			['aあｱ🏇⛄', 'aあｱ☃⛄', '☃', '🏇'],
			['aあｱ☃🏇', 'aあｱ☃⛄', '⛄', '🏇'],
			['aあｱ🏇🏇', 'aあｱ☃⛄', ['☃', '⛄'], '🏇'],
			['aあｱ🏇🐎', 'aあｱ☃⛄', ['☃', '⛄'], ['🏇', '🐎']],
		];
	}

	#[DataProvider('provider_replace')]
	public function test_replace(string $expected, string $source, string|array $oldValue, string|array $newValue)
	{
		$actual = Text::replace($source, $oldValue, $newValue);
		$this->assertSame($expected, $actual);
	}

	public function test_replace_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::replace('aaa', 'a', ['a']);
		$this->fail();
	}


	public static function provider_replace_array()
	{
		return [
			['______', 'ABCABC', ['A', 'B', 'C'], '_'],
			['___abc', 'ABCabc', ['A', 'B', 'C'], '_'],
		];
	}

	#[DataProvider('provider_replace_array')]
	public function test_replace_array(string $expected, string $source, string|array $oldValue, string|array $newValue)
	{
		$actual = Text::replace($source, $oldValue, $newValue);
		$this->assertSame($expected, $actual);
	}

	public static function provider_repeat()
	{
		return [
			['', 'a', 0],
			['a', 'a', 1],
			['aa', 'a', 2],
		];
	}

	#[DataProvider('provider_repeat')]
	public function test_repeat(string $expected, string $value, int $count)
	{
		$actual = Text::repeat($value, $count);
		$this->assertSame($expected, $actual);
	}

	public function test_repeat_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::repeat('', -1);
		$this->fail();
	}

	public static function provider_toCharacters()
	{
		return [
			[[' '], ' '],
			[['a', 'b', 'c'], 'abc'],
			[['あ', 'い', 'う'], 'あいう'],
			[['☃', '⛄', '𩸽', '🏇'], '☃⛄𩸽🏇'],
		];
	}

	#[DataProvider('provider_toCharacters')]
	public function test_toCharacters(array $expected, string $value)
	{
		$actual = Text::toCharacters($value);
		$this->assertSame($expected, $actual);
	}

	public function test_toCharacters_throw()
	{
		$this->expectException(ArgumentException::class);
		Text::toCharacters('');
		$this->fail();
	}
}
