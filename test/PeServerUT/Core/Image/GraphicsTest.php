<?php

declare(strict_types=1);

namespace PeServerUT\Core\Image;

use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Size;
use PeServer\Core\IO\File;
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

		$imageBinary = $graphics->save(ImageSetting::png());

		$newGraphics = Graphics::load($imageBinary);
		$newImageBinary = $newGraphics->save(ImageSetting::png());

		$this->assertSame($imageBinary->raw, $newImageBinary->raw);
	}

	public function test_open()
	{
		$testDir = $this->testDir();
		$imagePath = $testDir->newPath(__FUNCTION__);

		$graphics = Graphics::create(new Size(100, 100));
		$graphics->fillRectangle(new RgbColor(255, 0, 0), new Rectangle(new Point(0, 0), $graphics->getSize()));
		$imageBinary = $graphics->save(ImageSetting::png());
		File::writeContent($imagePath, $imageBinary);

		$graphics2 = Graphics::open($imagePath);
		$imageBinary2 = $graphics2->save(ImageSetting::png());

		$this->assertSame($imageBinary->raw, $imageBinary2->raw);
	}

	// public function test_saveHtmlSource()
	// {
	// 	$expected = 'data:image/gif;base64,R0lGODdhAQABAIAAAAQCBAAAACwAAAAAAQABAAACAkQBADs=';

	// 	$graphics = Graphics::create(new Size(1, 1));
	// 	$graphics->drawRectangle(new RgbColor(0, 0, 0), new Rectangle(new Point(0, 0), new Size(1, 1)));

	// 	$actual = $graphics->saveHtmlSource(new ImageSetting(ImageType::Gif, []));

	// 	$this->assertSame($expected, $actual);
	// }

	public function test_clone()
	{
		$graphics = Graphics::create(new Size(100, 100));
		$graphics->fillRectangle(new RgbColor(0, 255, 0), new Rectangle(new Point(0, 0), $graphics->getSize()));
		$imageBinary = $graphics->save(ImageSetting::png());

		$graphics2 = $graphics->clone();
		$imageBinary2 = $graphics2->save(ImageSetting::png());

		$this->assertSame($imageBinary->raw, $imageBinary2->raw);
	}

	public function test_dpi()
	{
		$graphics = Graphics::create(new Size(100, 100));

		$actual1 = $graphics->getDpi();
		$this->assertSame(96, $actual1->width);
		$this->assertSame(96, $actual1->height);

		$graphics->setDpi(new Size(72, 72));
		$actual2 = $graphics->getDpi();
		$this->assertSame(72, $actual2->width);
		$this->assertSame(72, $actual2->height);
	}
}
