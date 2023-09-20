<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServerUT\TestClass;
use PeServerUT\Data;
use PeServer\Core\Image\Point;

class PointTest extends TestClass
{
	public function test_empty()
	{
		$point = Point::empty();
		$this->assertSame(0, $point->x);
		$this->assertSame(0, $point->y);
	}

	public function test_serializable()
	{
		$tests = [
			new Point(123, 456),
		];
		foreach ($tests as $test) {
			$s = serialize($test);
			$actual = unserialize($s);
			$this->assertSame($test->x, $actual->x, (string)$actual->x);
			$this->assertSame($test->y, $actual->y, (string)$actual->y);
		}
	}

	public function test___toString()
	{
		$point1 = Point::empty();
		$this->assertSame('PeServer\Core\Image\Point: 0,0', (string)$point1);

		$point2 = new Point(1, 2);
		$this->assertSame('PeServer\Core\Image\Point: 1,2', (string)$point2);
	}
}
