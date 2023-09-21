<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\ImageType;
use PeServer\Core\Throws\ImageException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class ImageTypeTest extends TestClass
{
	public function test_toExtension()
	{
		$tests = [
			new Data('jpeg', ImageType::Jpeg, false),
			new Data('.jpeg', ImageType::Jpeg, true),
		];
		foreach ($tests as $test) {
			$actual = $test->args[0]->toExtension($test->args[1]);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_toExtension_throw()
	{
		$this->expectException(ImageException::class);
		ImageType::Auto->toExtension();
		$this->fail();
	}
}
