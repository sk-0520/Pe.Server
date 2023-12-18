<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServerTest\TestClass;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;

class DirectoryTest extends TestClass
{
	public function test_createDirectory()
	{
		$path = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__);

		$this->assertTrue(Directory::createDirectory($path));
		$this->assertFalse(Directory::createDirectory($path));
	}

	public function test_createDirectoryIfNotExists()
	{
		$path = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__);

		$this->assertTrue(Directory::createDirectoryIfNotExists($path));
		$this->assertFalse(Directory::createDirectoryIfNotExists($path));
	}

	public function test_createParentDirectoryIfNotExists()
	{
		$dir = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__);
		$file = __FUNCTION__ . '.txt';
		$path = Path::combine($dir,$file);

		$this->assertTrue(Directory::createParentDirectoryIfNotExists($path));
		$this->assertTrue(IOUtility::exists($dir));
		$this->assertFalse(IOUtility::exists($path));
		$this->assertFalse(Directory::createParentDirectoryIfNotExists($path));
	}
}
