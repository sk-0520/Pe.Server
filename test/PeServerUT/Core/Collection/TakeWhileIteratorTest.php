<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collection;

use ArrayIterator;
use PeServer\Core\Collection\TakeWhileIterator;
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
