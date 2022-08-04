<?php

declare(strict_types=1);

namespace PeServerTest\Core\Image;

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
}
