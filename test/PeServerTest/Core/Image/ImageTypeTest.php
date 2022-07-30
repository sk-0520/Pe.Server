<?php

declare(strict_types=1);

namespace PeServerTest\Core\Image;

use PeServer\Core\Image\ImageType;
use PeServer\Core\Throws\ImageException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class ImageTypeTest extends TestClass
{
	public function test_toExtension()
	{
		$tests = [
			new Data('jpeg', ImageType::JPEG, false),
			new Data('.jpeg', ImageType::JPEG, true),
		];
		foreach ($tests as $test) {
			$actual = ImageType::toExtension(...$test->args);
			$this->assertEquals($test->expected, $actual, $test->str());
		}
	}

	public function test_toExtension_throw()
	{
		$this->expectException(ImageException::class);
		ImageType::toExtension(ImageType::AUTO);
		$this->fail();
	}
}
