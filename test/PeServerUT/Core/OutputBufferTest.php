<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\OutputBufferException;
use PeServerUT\TestClass;

class OutputBufferTest extends TestClass
{
	public function test_get()
	{
		$actual = OutputBuffer::get(function () {
			echo 'abc';
		});
		$this->assertSame('abc', $actual->getRaw());
	}

	public function test_getContents()
	{
		$ob = new OutputBuffer();

		echo 'ABC';
		$actual1 = $ob->getContents();
		$this->assertSame('ABC', $actual1->getRaw());

		echo 'DEF';
		$actual2 = $ob->getContents();
		$this->assertSame('ABCDEF', $actual2->getRaw());

		$ob->dispose();
	}
}
