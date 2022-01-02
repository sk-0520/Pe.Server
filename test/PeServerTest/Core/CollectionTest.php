<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Collection;

class CollectionTest extends TestClass
{
	static $array = [
		1, 2, 3, 4, 5, 6
	];

	public function test_where()
	{
		$expected1 = [2, 4, 6];
		$actual1 = Collection::from(self::$array)
			->where(function ($i) {
				return $i % 2 == 0;
			})
			->toArray();
		$this->assertSame($expected1, $actual1);
	}

	public function test_any()
	{
		$this->assertTrue(Collection::from(self::$array)->any());
		$this->assertFalse(Collection::from([])->any());

		$this->assertTrue(Collection::from(self::$array)->any(function ($i) {
			return 6 <= $i;
		}));
		$this->assertFalse(Collection::from(self::$array)->any(function ($i) {
			return 6 < $i;
		}));
	}

	public function test_all()
	{
		$this->assertTrue(Collection::from(self::$array)->all(function ($i) {
			return $i <= 6;
		}));
		$this->assertFalse(Collection::from(self::$array)->all(function ($i) {
			return $i < 6;
		}));
	}
}
