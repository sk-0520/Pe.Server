<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Bytes;
use PeServer\Core\Throws\ArgumentException;

class BytesTest extends TestClass
{
	public function test_getRaw()
	{
		$bytes = new Bytes("a\0b\0");
		$this->assertEquals("a\0b\0", $bytes->getRaw());
	}

	public function test_getLength()
	{
		$bytes = new Bytes("a\0b\0");
		$this->assertEquals(4, $bytes->getLength());
	}

	public function test_toHex()
	{
		$tests = [
			new Data("01", "\x01"),
			new Data("0110", "\x01\x10"),
			new Data("0110ff", "\x01\x10\xff"),
		];
		foreach ($tests as $test) {
			$bytes = new Bytes(...$test->args);
			$actual = $bytes->toHex();
			$this->assertEquals($test->expected, $actual);
		}
	}

	public function test_base64()
	{
		$bytes = new Bytes("a\0b\0");
		$base64 = $bytes->toBase64();
		$actual = Bytes::fromBase64($base64);
		$this->assertEquals($bytes->getRaw(), $actual->getRaw());
		$this->assertEquals($base64, $actual->toBase64());
	}

	public function test_base64_error()
	{
		$this->expectException(ArgumentException::class);
		Bytes::fromBase64('@@@@@@');
		$this->fail();
	}


}
