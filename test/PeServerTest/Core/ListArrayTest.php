<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\TestClass;
use PeServer\Core\ListArray;

class ListArrayTest extends TestClass
{
	public function test()
	{
		$list = new ListArray();
		$this->assertSame(0, $list->getCount());

		$list->add(1);
		$this->assertSame(1, $list->getCount());

		$list->addRange([2, 3]);
		$this->assertSame(3, $list->getCount());
		$this->assertSame([1, 2, 3], $list->getArray());
	}
}
