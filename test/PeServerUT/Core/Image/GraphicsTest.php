<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\Size;
use PeServerTest\TestClass;

class GraphicsTest extends TestClass
{
	public function test_size()
	{
		$expected = new Size(100, 100);
		$graphics = Graphics::create($expected);
		$actual = $graphics->getSize();
		$this->assertSame($expected->width, $actual->width);
		$this->assertSame($expected->height, $actual->height);
	}

	public function test_pixel()
	{
		$expected = new RgbColor(0xff, 0xff, 0xff, 0x10);
		$size = new Size(100, 100);
		$point = new Point(50, 50);
		$graphics = Graphics::create($size);

		$graphics->setPixel($point, $expected);
		$actual = $graphics->getPixel($point);

		$this->assertSame($expected->red, $actual->red);
		$this->assertSame($expected->green, $actual->green);
		$this->assertSame($expected->blue, $actual->blue);
		$this->assertSame($expected->alpha, $actual->alpha);
	}

	public function test_load()
	{
		$graphics = Graphics::create(new Size(100, 100));

		$graphics->setPixel(new Point(10, 10), new RgbColor(0xff, 0xff, 0xff));

		$imageBinary = $graphics->exportImage(ImageSetting::png());

		$newGraphics = Graphics::load($imageBinary);
		$newImageBinary = $newGraphics->exportImage(ImageSetting::png());

		$this->assertSame($imageBinary->raw, $newImageBinary->raw);
	}
}
