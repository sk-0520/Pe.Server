<?php

declare(strict_types=1);

namespace PeServerTest\Core\Image;

use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Size;
use PeServerTest\Data;
use PeServerTest\TestClass;

class RectangleTest extends TestClass
{
	public function test_serializable()
	{
		$tests = [
			new Rectangle(new Point(1, 2), new Size(3, 4)),
		];
		foreach ($tests as $test) {
			$s = serialize($test);
			$actual = unserialize($s);
			$this->assertSame($test->point->x, $actual->point->x, (string)$actual->point->x);
			$this->assertSame($test->point->y, $actual->point->y, (string)$actual->point->y);
			$this->assertSame($test->size->width, $actual->size->width, (string)$actual->size->width);
			$this->assertSame($test->size->height, $actual->size->height, (string)$actual->size->height);
		}
	}
}
