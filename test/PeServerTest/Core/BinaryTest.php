<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NullByteStringException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class BinaryTest extends TestClass
{
	public function test_getRaw()
	{
		$binary = new Binary("a\0b\0");
		$this->assertSame("a\0b\0", $binary->getRaw());
	}

	public function test_getLength()
	{
		$binary = new Binary("a\0b\0");
		$this->assertSame(4, $binary->getLength());
	}

	public function provider_isEquals()
	{
		return [
			[false, "\x00", ""],
			[false, "", "\x00"],
			[true, "\x00", "\x00"],
		];
	}

	/** @dataProvider provider_isEquals */
	public function test_isEquals($expected, $a, $b)
	{
		$aBin = new Binary($a);
		$bBin = new Binary($b);
		$this->assertSame($expected, $aBin->isEquals($bBin));
		$this->assertSame($expected, $bBin->isEquals($aBin));
	}

	public function test_toHex()
	{
		$tests = [
			new Data("01", "\x01"),
			new Data("0110", "\x01\x10"),
			new Data("0110ff", "\x01\x10\xff"),
		];
		foreach ($tests as $test) {
			$binary = new Binary(...$test->args);
			$actual = $binary->toHex();
			$this->assertSame($test->expected, $actual, $test->str());
		}
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
		$this->assertSame($binary->getRaw(), $actual->getRaw());
		$this->assertSame($base64, $actual->toBase64());
	}

	public function test_base64_throw()
	{
		$this->expectException(ArgumentException::class);
		Binary::fromBase64('@@@@@@');
		$this->fail();
	}

	public function test_hasNull()
	{
		$tests = [
			new Data(false, ""),
			new Data(false, "abc"),
			new Data(true, "\0abc"),
			new Data(true, "abc\0"),
			new Data(true, "a\0c"),
		];
		foreach ($tests as $test) {
			$binary = new Binary(...$test->args);
			$actual = $binary->hasNull();
			$this->assertSame($test->expected, $actual, $test->str());
		}
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
		$this->expectException(NullByteStringException::class);
		$binary = new Binary("\0");
		$actual = $binary->toString();
		$this->fail();
	}

	public function test___toString()
	{
		$tests = [
			new Data("abc", "abc"),
			new Data("00616263", "\0abc"),
		];
		foreach ($tests as $test) {
			$binary = new Binary(...$test->args);
			$actual = (string)$binary;
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
