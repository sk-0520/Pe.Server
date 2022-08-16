<?php

declare(strict_types=1);

namespace PeServerTest\Core\IO;

use PeServer\Core\IO\PathParts;
use PeServer\Core\IO\Path;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class PathTest extends TestClass
{
	public function test_combine()
	{
		$sep = DIRECTORY_SEPARATOR;
		$tests = [
			new Data("a${sep}b", "a", "b"),
			new Data("a${sep}b", "a", '', "b"),
			new Data("a${sep}b${sep}c", '', "a", 'b', "c", ''),
			new Data("${sep}", "${sep}"),
			new Data("abc", 'abc'),
			new Data("abc${sep}def${sep}GHI", 'abc', 'def', 'ghi', '..', '.', 'GHI'),
			new Data("${sep}abc${sep}def${sep}GHI", "${sep}abc", 'def', 'ghi', '..', '.', 'GHI'),
		];
		foreach ($tests as $test) {
			$actual = Path::combine(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getDirectoryPath()
	{
		$tests = [
			new Data(".", "name"),
			new Data("path", "path/name"),
			new Data("path1/path2", "path1/path2/name"),
			new Data("/path1/path2", "/path1/path2/name"),
		];
		foreach ($tests as $test) {
			$actual = Path::getDirectoryPath(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getFileName()
	{
		$tests = [
			new Data("name", "name"),
			new Data("name", "path/name"),
			//new Data("name", "path\\name"), // winだけ？
			new Data("", "/"),
			new Data(".", "/."),
		];
		foreach ($tests as $test) {
			$actual = Path::getFileName(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getFileExtension()
	{
		$tests = [
			new Data("", "", false),
			new Data("", "  ", false),
			new Data("", ".", false),
			new Data("txt", "a.txt", false),
			new Data("txt", "a.b.txt", false),
			new Data("txt", ".txt", false),
			new Data("", "txt", false),

			new Data("", "", true),
			new Data("", "  ", true),
			new Data(".", ".", true),
			new Data(".txt", "a.txt", true),
			new Data(".txt", "a.b.txt", true),
			new Data(".txt", ".txt", true),
			new Data("", "txt", true),
		];
		foreach ($tests as $test) {
			$actual = Path::getFileExtension(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_getFileNameWithoutExtension()
	{
		$tests = [
			new Data("", ""),
			new Data(" ", " "),
			new Data("a", "a.b"),
			new Data("a.b", "a.b.c"),
			new Data("style", "style.css"),
			new Data("style", "/dir/style.css"),
			new Data("", ".htaccess"),
			new Data("", "."),
			new Data(".", ".."),
		];
		foreach ($tests as $test) {
			$actual = Path::getFileNameWithoutExtension(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_toParts()
	{
		$tests = [
			new Data(new PathParts('/a', 'b.c', 'b', 'c'), '/a/b.c'),
			new Data(new PathParts('.', 'a.b', 'a', 'b'), 'a.b'),
			new Data(new PathParts('.', 'a', 'a', ''), 'a'),
			new Data(new PathParts('.', '.htaccess', '', 'htaccess'), '.htaccess'),
			new Data(new PathParts('/🍳/🚽', '💩.🚮', '💩', '🚮'), '/🍳/🚽/💩.🚮'),
		];
		foreach ($tests as $test) {
			$actual = Path::toParts(...$test->args);
			$this->assertSame($test->expected->directory, $actual->directory, $test->str());
			$this->assertSame($test->expected->fileName, $actual->fileName, $test->str());
			$this->assertSame($test->expected->fileNameWithoutExtension, $actual->fileNameWithoutExtension, $test->str());
			$this->assertSame($test->expected->extension, $actual->extension, $test->str());
		}
	}

	public function test_toParts_empty_throw()
	{
		$this->expectException(ArgumentException::class);
		Path::toParts('');
		$this->fail();
	}
}
