<?php

declare(strict_types=1);

namespace PeServerTest\Core\Image;

use PeServerTest\TestClass;
use PeServerTest\Data;
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
			$this->assertEquals($test->width, $actual->width, (string)$actual->width);
			$this->assertEquals($test->height, $actual->height, (string)$actual->height);
		}
	}
}
