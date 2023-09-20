<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServerUT\TestClass;
use PeServerUT\Data;
use PeServer\Core\Image\Size;

class SizeTest extends TestClass
{
	public function test_serializable()
	{
		$tests = [
			new Size(123, 456),
		];
		foreach ($tests as $test) {
			$s = serialize($test);
			$actual = unserialize($s);
			$this->assertSame($test->width, $actual->width, (string)$actual->width);
			$this->assertSame($test->height, $actual->height, (string)$actual->height);
		}
	}
}
