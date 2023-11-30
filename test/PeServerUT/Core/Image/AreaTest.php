<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServerTest\TestClass;
use PeServerTest\Data;
use PeServer\Core\Image\Area;
use PeServer\Core\Image\Point;

class AreaTest extends TestClass
{
	public function test_serializable()
	{
		$tests = [
			new Area(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8)),
		];
		foreach ($tests as $test) {
			$s = serialize($test);
			$actual = unserialize($s);
			$this->assertSame($test->leftTop->x, $actual->leftTop->x, (string)$test->leftTop->x);
			$this->assertSame($test->leftTop->y, $actual->leftTop->y, (string)$test->leftTop->y);
			$this->assertSame($test->leftBottom->x, $actual->leftBottom->x, (string)$test->leftBottom->x);
			$this->assertSame($test->leftBottom->y, $actual->leftBottom->y, (string)$test->leftBottom->y);
			$this->assertSame($test->rightTop->x, $actual->rightTop->x, (string)$test->rightTop->x);
			$this->assertSame($test->rightTop->y, $actual->rightTop->y, (string)$test->rightTop->y);
			$this->assertSame($test->rightBottom->x, $actual->rightBottom->x, (string)$test->rightBottom->x);
			$this->assertSame($test->rightBottom->y, $actual->rightBottom->y, (string)$test->rightBottom->y);
		}
	}

	public function test___toString() {
		$tests = [
			new Data('PeServer\Core\Image\Area(leftTop:PeServer\Core\Image\Point(x:1,y:2),leftBottom:PeServer\Core\Image\Point(x:3,y:4),rightBottom:PeServer\Core\Image\Point(x:5,y:6),rightTop:PeServer\Core\Image\Point(x:7,y:8))', new Area(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(7, 8))),
		];
		foreach ($tests as $test) {
			$this->assertSame($test->expected, (string)$test->args[0]);
		}
	}
}
