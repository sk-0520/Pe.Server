<?php

declare(strict_types=1);

namespace PeServerTest\Core\Collections;

use ArrayIterator;
use PeServer\Core\Collections\SelectManyIterator;
use PeServer\Core\Throws\CallbackTypeError;
use PeServerTest\TestClass;

class SelectManyIteratorTest extends TestClass
{
	public function test_construct_throw()
	{
		$this->expectException(CallbackTypeError::class);
		new SelectManyIterator(new ArrayIterator([]), '(^v^)');
		$this->fail();
	}
}
