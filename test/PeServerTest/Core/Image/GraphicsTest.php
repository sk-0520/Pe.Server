<?php

declare(strict_types=1);

namespace PeServerTest\Core\Image;

use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageOption;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\RgbColor;
use PeServer\Core\Image\Size;
use PeServerTest\TestClass;

class GraphicsTest extends TestClass
{
	public function test_size()
	{
		$expected = new Size(100, 100);
		$graphics = Graphics::create($expected);
		$actual = $graphics->getSize();
		$this->assertEquals($expected->width, $actual->width);
		$this->assertEquals($expected->height, $actual->height);
	}

	public function test_pixel()
	{
		$expected = new RgbColor(0xff, 0xff, 0xff);
		$size = new Size(100, 100);
		$point = new Point(50, 50);
		$graphics = Graphics::create($size);

		$graphics->setPixel($point, $expected);
		$actual = $graphics->getPixel($point);

		$this->assertEquals($expected->red, $actual->red);
		$this->assertEquals($expected->green, $actual->green);
		$this->assertEquals($expected->blue, $actual->blue);
	}

	public function test_load()
	{
		$graphics = Graphics::create(new Size(100, 100));

		$graphics->setPixel(new Point(10, 10), new RgbColor(0xff, 0xff, 0xff));

		$imageBinary = $graphics->toImage(ImageOption::png());

		$newGraphics = Graphics::load($imageBinary);
		$newImageBinary = $newGraphics->toImage(ImageOption::png());

		$this->assertEquals($imageBinary->getRaw(), $newImageBinary->getRaw());
	}
}
