<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\ImageType;
use PeServer\Core\Throws\ImageException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class ImageTypeTest extends TestClass
{
	public static function provider_toExtension()
	{
		return [
			['jpeg', ImageType::Jpeg, false],
			['.jpeg', ImageType::Jpeg, true],
		];
	}

	#[DataProvider('provider_toExtension')]
	public function test_toExtension(string $expected, ImageType $type, bool $dot)
	{
		$actual = $type->toExtension($dot);
		$this->assertSame($expected, $actual);
	}

	public function test_toExtension_throw()
	{
		$this->expectException(ImageException::class);
		ImageType::Auto->toExtension();
		$this->fail();
	}
}
