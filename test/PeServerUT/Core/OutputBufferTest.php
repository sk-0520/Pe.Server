<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\OutputBufferException;
use PeServerTest\TestClass;

class OutputBufferTest extends TestClass
{
	public function test_get()
	{
		$actual = OutputBuffer::get(function () {
			echo 'abc';
		});
		$this->assertSame('abc', $actual->raw);
	}

	public function test_getContents()
	{
		$ob = new OutputBuffer();

		echo 'ABC';
		$actual1 = $ob->getContents();
		$this->assertSame('ABC', $actual1->raw);

		echo 'DEF';
		$actual2 = $ob->getContents();
		$this->assertSame('ABCDEF', $actual2->raw);

		$ob->dispose();
	}

	public function test_ByteCount()
	{
		$ob = new OutputBuffer();

		echo 'ABC';
		$actual1 = $ob->getByteCount();
		$this->assertSame(3, $actual1);

		echo 'DEF';
		$actual2 = $ob->getByteCount();
		$this->assertSame(6, $actual2);

		$ob->dispose();
	}

	public function test_nest_1()
	{
		$actualRoot = OutputBuffer::get(function () {
			echo 'abc';
			OutputBuffer::get(function () {
				echo 'def';
			});
			echo 'ghi';
		});
		$this->assertSame('abcghi', $actualRoot->raw); //cspell:disable-line
	}

	public function test_nest_2()
	{
		$actualRoot = OutputBuffer::get(function () {
			echo 'abc';
			echo OutputBuffer::get(function () {
				echo 'def';
			})->raw;
			echo 'ghi';
		});
		$this->assertSame('abcdefghi', $actualRoot->raw); //cspell:disable-line
	}
}
