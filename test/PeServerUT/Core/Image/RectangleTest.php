<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Size;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class RectangleTest extends TestClass
{
	public function test_normal()
	{
		$rect = new Rectangle(new Point(1, 2), new Size(3, 4));

		$this->assertSame(1, $rect->point->x);
		$this->assertSame(2, $rect->point->y);
		$this->assertSame(3, $rect->size->width);
		$this->assertSame(4, $rect->size->height);

		$this->assertSame(1, $rect->left());
		$this->assertSame(2, $rect->top());

		$this->assertSame(1 + 3, $rect->right());
		$this->assertSame(2 + 4, $rect->bottom());
	}

	public static function provider_serializable()
	{
		return [
			[new Rectangle(new Point(1, 2), new Size(3, 4))],
		];
	}

	#[DataProvider('provider_serializable')]
	public function test_serializable(Rectangle $test)
	{
		$s = serialize($test);
		$actual = unserialize($s);
		$this->assertSame($test->point->x, $actual->point->x, (string)$actual->point->x);
		$this->assertSame($test->point->y, $actual->point->y, (string)$actual->point->y);
		$this->assertSame($test->size->width, $actual->size->width, (string)$actual->size->width);
		$this->assertSame($test->size->height, $actual->size->height, (string)$actual->size->height);
	}

	public function test___toString()
	{
		$rectangle1 = new Rectangle(new Point(1, 2), new Size(3, 4));
		$this->assertSame('PeServer\Core\Image\Rectangle(point:PeServer\Core\Image\Point(x:1,y:2),size:PeServer\Core\Image\Size(width:3,height:4))', (string)$rectangle1);
	}
}
