<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\PathUtility;

class PathUtilityTest extends TestClass
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
			$actual = PathUtility::combine(...$test->args);
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
			$actual = PathUtility::getDirectoryPath(...$test->args);
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
			$actual = PathUtility::getFileName(...$test->args);
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
			$actual = PathUtility::getFileExtension(...$test->args);
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
			$actual = PathUtility::getFileNameWithoutExtension(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
