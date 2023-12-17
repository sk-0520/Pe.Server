<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\ImageInformation;
use PeServer\Core\Image\Size;
use PeServer\Core\Mime;
use PeServer\Core\Throws\ImageException;
use PeServerTest\TestClass;

class ImageInformationTest extends TestClass
{
	private const DIR = __DIR__ . DIRECTORY_SEPARATOR . 'ImageInformationTest' . '.data' . DIRECTORY_SEPARATOR;

	public function test_load_bmp()
	{
		$actual = ImageInformation::load(self::DIR . DIRECTORY_SEPARATOR . 'image.bmp');
		$this->assertSame(ImageType::Bmp, $actual->type);
		$this->assertSame(ImageType::Bmp->toMime(), $actual->mime);
		$this->assertSame(512, $actual->size->width);
		$this->assertSame(256, $actual->size->height);
	}

	public function test_load_png()
	{
		$actual = ImageInformation::load(self::DIR . DIRECTORY_SEPARATOR . 'image.png');
		$this->assertSame(ImageType::Png, $actual->type);
		$this->assertSame(ImageType::Png->toMime(), $actual->mime);
		$this->assertSame(256, $actual->size->width);
		$this->assertSame(512, $actual->size->height);
	}

	public function test_load_throw_NULL()
	{
		$this->expectException(ImageException::class);
		ImageInformation::load(self::DIR . DIRECTORY_SEPARATOR . 'NUL');
		$this->fail();
	}

	public function test_load_throw_php()
	{
		$this->expectException(ImageException::class);
		ImageInformation::load(__FILE__);
		$this->fail();
	}
}
