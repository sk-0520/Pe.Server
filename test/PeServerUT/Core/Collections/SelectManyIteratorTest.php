<?php

declare(strict_types=1);

namespace PeServerUT\Core\Collections;

use ArrayIterator;
use PeServer\Core\Collection\SelectManyIterator;
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
