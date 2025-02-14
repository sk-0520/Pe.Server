<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\OrderBy;
use PeServerTest\TestClass;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Throws\IOException;

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

	private static function makeTree(string $base)
	{
		$items = [
			'a/b/c.txt',
			'a/b.txt',
			'a/b/z',
			'a.txt',
		];

		foreach ($items as $item) {
			$path = Path::combine($base, $item);
			if (Path::getFileExtension($item)) {
				Directory::createParentDirectoryIfNotExists($path);
				File::createEmptyFileIfNotExists($path);
			} else {
				Directory::createDirectory($path);
			}
		}
	}

	public function test_getChildren()
	{
		$sep = DIRECTORY_SEPARATOR;
		$testDir = $this->testDir();
		$this->makeTree($testDir->path);

		$actual1 = Arr::sortByValue(Arr::map(Directory::getChildren($testDir->path, false), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(2, $actual1);
		$this->assertSame('a', $actual1[0]);
		$this->assertSame('a.txt', $actual1[1]);

		$actual2 = Arr::sortByValue(Arr::map(Directory::getChildren($testDir->path, true), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(6, $actual2);
		$this->assertSame('a', $actual2[0]);
		$this->assertSame('a.txt', $actual2[1]);
		$this->assertSame("a{$sep}b", $actual2[2]);
		$this->assertSame("a{$sep}b.txt", $actual2[3]);
		$this->assertSame("a{$sep}b{$sep}c.txt", $actual2[4]);
		$this->assertSame("a{$sep}b{$sep}z", $actual2[5]);
	}

	public function test_getChildren_throw()
	{
		$items = Directory::getChildren("\n", false);
		$this->assertCount(0, $items);
	}


	public function test_getFiles()
	{
		$sep = DIRECTORY_SEPARATOR;
		$testDir = $this->testDir();
		$this->makeTree($testDir->path);

		$actual1 = Arr::sortByValue(Arr::map(Directory::getFiles($testDir->path, false), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(1, $actual1);
		$this->assertSame('a.txt', $actual1[0]);

		$actual2 = Arr::sortByValue(Arr::map(Directory::getFiles($testDir->path, true), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(3, $actual2);
		$this->assertSame('a.txt', $actual2[0]);
		$this->assertSame("a{$sep}b.txt", $actual2[1]);
		$this->assertSame("a{$sep}b{$sep}c.txt", $actual2[2]);
	}

	public function test_getDirectories()
	{
		$sep = DIRECTORY_SEPARATOR;
		$testDir = $this->testDir();
		$this->makeTree($testDir->path);

		$actual1 = Arr::sortByValue(Arr::map(Directory::getDirectories($testDir->path, false), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(1, $actual1);
		$this->assertSame('a', $actual1[0]);

		$actual2 = Arr::sortByValue(Arr::map(Directory::getDirectories($testDir->path, true), fn($a) => Text::substring($a, Text::getLength($testDir->path) + 1)), OrderBy::Ascending);
		$this->assertCount(3, $actual2);
		$this->assertSame('a', $actual2[0]);
		$this->assertSame("a{$sep}b", $actual2[1]);
		$this->assertSame("a{$sep}b{$sep}z", $actual2[2]);
	}

	public function test_WorkingDirectory()
	{
		$actual1 = Directory::getCurrentWorkingDirectory();
		try {
			Directory::setWorkingDirectory("");
			$this->fail();
		} catch (IOException) {
			$this->success();
		}
		$actual2 = Directory::getCurrentWorkingDirectory();
		$this->assertSame($actual1, $actual2);
	}

	public function test_changeWorkingDirectory()
	{
		$dir = $this->testDir();
		$path = $dir->createDirectory("NEW");

		$actual1 = Directory::getCurrentWorkingDirectory();

		$working = Directory::changeWorkingDirectory($path);
		$actual2 = Directory::getCurrentWorkingDirectory();
		$this->assertSame($path, $actual2);
		$working->dispose();

		$actual3 = Directory::getCurrentWorkingDirectory();
		$this->assertSame($actual1, $actual3);
	}
}
