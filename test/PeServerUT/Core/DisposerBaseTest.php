<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IndexOutOfRangeException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\NullByteStringException;
use PeServer\Core\Throws\ObjectDisposedException;
use PeServerTest\TestClass;
use TypeError;

class DisposerBaseTest extends TestClass
{
	public function test_empty_dispose()
	{
		$empty = DisposerBase::empty();
		$this->assertFalse($empty->isDisposed());

		$empty->dispose();
		$this->assertTrue($empty->isDisposed());
	}

	public function test_throwIfDisposed_throw()
	{
		$obj = new class extends DisposerBase {
			public function method(): int
			{
				$this->throwIfDisposed();
				return 10;
			}
		};

		$this->assertSame(10, $obj->method());

		$obj->dispose();
		$this->expectException(ObjectDisposedException::class);
		$obj->method();
		$this->fail();
	}
}
