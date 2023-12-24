<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use ArrayIterator;
use PeServer\Core\Collection\SelectIterator;
use PeServer\Core\Throws\CallbackTypeError;
use PeServerTest\TestClass;

class SelectIteratorTest extends TestClass
{
	public function test_construct_throw()
	{
		$this->expectException(CallbackTypeError::class);
		new SelectIterator(new ArrayIterator([]), '(^v^)');
		$this->fail();
	}
}
