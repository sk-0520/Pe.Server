<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Binary;
use PeServer\Core\Throws\ArgumentException;

class BinaryTest extends TestClass
{
	public function test_getRaw()
	{
		$binary = new Binary("a\0b\0");
		$this->assertEquals("a\0b\0", $binary->getRaw());
	}

	public function test_getLength()
	{
		$binary = new Binary("a\0b\0");
		$this->assertEquals(4, $binary->getLength());
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
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_convert()
	{
		$tests = [
			new Data("0", "\x00", 2, 2),
			//new Data("1", "\x01", 2, 2), どうなったいいのか分かってない
		];
		foreach ($tests as $test) {
			$binary = new Binary($test->args[0]);
			$actual = $binary->convert($test->args[1], $test->args[2]);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_base64()
	{
		$binary = new Binary("a\0b\0");
		$base64 = $binary->toBase64();
		$actual = Binary::fromBase64($base64);
		$this->assertEquals($binary->getRaw(), $actual->getRaw());
		$this->assertEquals($base64, $actual->toBase64());
	}

	public function test_base64_error()
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
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}


}
