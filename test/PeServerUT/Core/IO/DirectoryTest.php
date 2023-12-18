<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServerTest\TestClass;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;

class DirectoryTest extends TestClass
{
	public function test_createDirectory()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertTrue(Directory::createDirectory($path));
		$this->assertFalse(Directory::createDirectory($path));
	}

	public function test_createDirectoryIfNotExists()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertTrue(Directory::createDirectoryIfNotExists($path));
		$this->assertFalse(Directory::createDirectoryIfNotExists($path));
	}

	public function test_createParentDirectoryIfNotExists()
	{
		$testDir = $this->testDir();
		$file = 'TEST' . DIRECTORY_SEPARATOR . __FUNCTION__ . '.txt';
		$path = $testDir->newPath($file);

		$this->assertTrue(Directory::createParentDirectoryIfNotExists($path));
		$this->assertTrue(IOUtility::exists($testDir->path));
		$this->assertFalse(IOUtility::exists($path));
		$this->assertFalse(Directory::createParentDirectoryIfNotExists($path));
	}

	public function test_exists()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertFalse(Directory::exists($path));

		Directory::createDirectory($path);
		$this->assertTrue(Directory::exists($path));

		$path2 = $testDir->newPath(__FUNCTION__ . '-2');
		$this->assertFalse(Directory::exists($path2));
		File::createEmptyFileIfNotExists($path2);
		$this->assertTrue(File::exists($path2));
		$this->assertFalse(Directory::exists($path2));
	}
}
