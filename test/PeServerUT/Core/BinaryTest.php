<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use TypeError;
use PeServer\Core\Binary;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\BinaryException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\NullByteStringException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class BinaryTest extends TestClass
{
	public function test_getRaw()
	{
		$binary = new Binary("a\0b\0");
		$this->assertSame("a\0b\0", $binary->raw);
	}

	public static function provider_getRange()
	{
		return [
			["\x01", "\x01", 0, 1],
			["\x01", "\x01\x10", 0, 1],
			["\x10", "\x01\x10", -1, null],
			["\x01\x10", "\x01\x10", 0, 2],
			["\x01\x10\xff", "\x01\x10\xff", 0, null],
			["\x10", "\x01\x10\xff", 1, 1],
			["\xff", "\x01\x10\xff", -1, 1],
			["\xff", "\x01\x10\xff", -1, null],
			["\x10\xff", "\x01\x10\xff", -2, null],
			["\x10", "\x01\x10\xff", -2, 1],
			["\x10\xff", "\x01\x10\xff", -2, 2],
			["\x10\xff", "\x01\x10\xff", 1, null],
			["\x10", "\x01\x10\xff", 1, 1],
			["\x10\xff", "\x01\x10\xff", 1, 4],
			["", "\x01\x10\xff", 10, 4],
		];
	}

	#[DataProvider('provider_getRange')]
	public function test_getRange(string $expected, string $input, int $index, ?int $length)
	{
		$binary = new Binary($input);
		$actual = $binary->getRange($index, $length);
		$this->assertSame($expected, $actual->raw);
	}


	public static function provider_isEquals()
	{
		return [
			[false, "\x00", ""],
			[false, "", "\x00"],
			[true, "\x00", "\x00"],
		];
	}

	#[DataProvider('provider_isEquals')]
	public function test_isEquals($expected, $a, $b)
	{
		$aBin = new Binary($a);
		$bBin = new Binary($b);
		$this->assertSame($expected, $aBin->isEquals($bBin));
		$this->assertSame($expected, $bBin->isEquals($aBin));
	}

	public static function provider_toHex()
	{
		return [
			["01", "\x01"],
			["0110", "\x01\x10"],
			["0110ff", "\x01\x10\xff"],
		];
	}

	#[DataProvider('provider_toHex')]
	public function test_toHex(string $expected, string $input)
	{
		$binary = new Binary($input);
		$actual = $binary->toHex();
		$this->assertSame($expected, $actual);
	}

	// public function test_convert()
	// {
	// 	$tests = [
	// 		new Data("0", "\x00", 2, 2),
	// 		//new Data("1", "\x01", 2, 2), どうなったいいのか分かってない
	// 	];
	// 	foreach ($tests as $test) {
	// 		$binary = new Binary($test->args[0]);
	// 		$actual = $binary->convert($test->args[1], $test->args[2]);
	// 		$this->assertSame($test->expected, $actual, $test->str());
	// 	}
	// }

	public function test_base64()
	{
		$binary = new Binary("a\0b\0");
		$base64 = $binary->toBase64();
		$actual = Binary::fromBase64($base64);
		$this->assertSame($binary->raw, $actual->raw);
		$this->assertSame($base64, $actual->toBase64());
	}

	public function test_base64_throw()
	{
		$this->expectException(ArgumentException::class);
		Binary::fromBase64('@@@@@@');
		$this->fail();
	}

	public static function provider_hasNull()
	{
		return [
			[false, ""],
			[false, "abc"],
			[true, "\0abc"],
			[true, "abc\0"],
			[true, "a\0c"],
		];
	}

	#[DataProvider('provider_hasNull')]
	public function test_hasNull(bool $expected, string $input)
	{
		$binary = new Binary($input);
		$actual = $binary->hasNull();
		$this->assertSame($expected, $actual);
	}

	public function test_toString()
	{
		$expected = 'a';
		$binary = new Binary($expected);
		$actual = $binary->toString();
		$this->assertSame($expected, $actual);
	}

	public function test_toString_throw()
	{
		$binary = new Binary("\0");
		$this->expectException(NullByteStringException::class);
		$binary->toString();
		$this->fail();
	}

	public function test_fromArray()
	{
		$actual = Binary::fromArray("C*", [115, 116, 114]);
		$this->assertSame("str", $actual->raw);
	}

	public function test_toArray()
	{
		$binary = new Binary("str");
		$actual = $binary->toArray('C*');
		$this->assertSame([1 => 115, 2 => 116, 3 => 114], $actual);
	}

	public function test_toArray_throw()
	{
		$binary = new Binary("");
		$this->expectException(BinaryException::class);
		$binary->toArray('あ');
	}

	public function test_array()
	{
		$binary = new Binary("A\x00a\xFF");
		$this->assertSame(0x41, $binary[0]);
		$this->assertSame(0x00, $binary[1]);
		$this->assertSame(0x61, $binary[2]);
		$this->assertSame(0xff, $binary[3]);

		$this->assertTrue(isset($binary[0]));
		$this->assertTrue(isset($binary[1]));
		$this->assertTrue(isset($binary[2]));
		$this->assertTrue(isset($binary[3]));

		$this->assertFalse(isset($binary[-1]));
		$this->assertFalse(isset($binary[4]));
		$this->assertFalse(isset($binary['A']));

		try {
			$binary['A'];
			$this->fail();
		} catch (TypeError) {
			$this->success();
		}

		try {
			$binary[-1];
			$this->fail();
		} catch (IndexOutOfRangeException) {
			$this->success();
		}

		try {
			$binary[4];
			$this->fail();
		} catch (IndexOutOfRangeException) {
			$this->success();
		}

		try {
			unset($binary[0]);
			$this->fail();
		} catch (NotSupportedException) {
			$this->success();
		}

		try {
			$binary[0] = 0xff;
			$this->fail();
		} catch (NotSupportedException) {
			$this->success();
		}
	}

	public function test_foreach()
	{
		$binary = new Binary("abc");
		$expected = ['a', 'b', 'c'];
		$i = 0;
		foreach ($binary as $c) {
			$this->assertSame($expected[$i++], $c);
		}
	}

	public function test_count()
	{
		$binary = new Binary("a\0b\0");
		$this->assertSame(4, $binary->count());
	}

	public static function provider___toString()
	{
		return [
			["abc", "abc"],
			["00616263", "\0abc"],
		];
	}

	#[DataProvider('provider___toString')]
	public function test___toString(string $expected, string $input)
	{
		$binary = new Binary($input);
		$actual = (string)$binary;
		$this->assertSame($expected, $actual);
	}
}
