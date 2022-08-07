<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;


class PaginationTest extends TestClass
{
	public function test_constructor_throw()
	{
		$this->expectException(ArgumentException::class);
		new Pagination(0, -1, 0);
		$this->fail();
	}

	public function provider_constructor_shortcutTotalCount()
	{
		return [
			[0, 0, 0],
			[0, 1, 0],
			[1, 1, 1],
			[2, 1, 2],
			[1, 10, 10],
			[2, 10, 20],
			[1, 10, 9],
			[0, 10, 0],
			[1, 10, 1],
			[2, 10, 11],
			[2, 10, 19],
			[2, 10, 20],
			[3, 10, 21],
		];
	}
	/** @dataProvider provider_constructor_shortcutTotalCount */
	public function test_constructor_shortcutTotalCount($expected, $itemCount, $totalCount)
	{
		$pager = new Pagination(0, $itemCount, $totalCount);
		$this->assertSame($expected, $pager->shortcutTotalCount);
	}

	public static function provider_getShortcuts()
	{
		return [
			[1, 1, 1, 1],
			[10, 1, 10, 10],
			[5, 2, 10, 5],
			[4, 3, 10, 5],
		];
	}

	/** @dataProvider provider_getShortcuts */
	public function test_getShortcuts_count($expected, $itemCount, $totalCount, $shortcutMaxCount)
	{
		$pager = new Pagination(0, $itemCount, $totalCount, shortcutMaxCount: $shortcutMaxCount);
		$this->assertSame($expected, ArrayUtility::getCount($pager->getShortcuts()));
	}
}
