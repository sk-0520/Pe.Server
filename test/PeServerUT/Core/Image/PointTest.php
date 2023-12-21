<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServerTest\TestClass;
use PeServerTest\Data;
use PeServer\Core\Image\Point;

class PointTest extends TestClass
{
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
		$point1 = new Point(0, 0);
		$this->assertSame('PeServer\Core\Image\Point(x:0,y:0)', (string)$point1);

		$point2 = new Point(1, 2);
		$this->assertSame('PeServer\Core\Image\Point(x:1,y:2)', (string)$point2);
	}
}
