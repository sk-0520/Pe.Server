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
}
