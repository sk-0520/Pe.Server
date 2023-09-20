<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use ArrayIterator;
use PeServer\Core\Collections\TakeWhileIterator;
use PeServer\Core\Throws\CallbackTypeError;
use PeServerTest\TestClass;

class TakeWhileIteratorTest extends TestClass
{
	public function test_construct_throw()
	{
		$this->expectException(CallbackTypeError::class);
		new TakeWhileIterator(new ArrayIterator([]), '(^v^)');
		$this->fail();
	}
}
