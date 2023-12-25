<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\IO\PathParts;
use PeServer\Core\IO\Path;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class PathTest extends TestClass
{
	public static function provider_combine()
	{
		$sep = DIRECTORY_SEPARATOR;
		return [
			["a{$sep}b", "a", "b"],
			["a{$sep}b", "a", '', "b"],
			["a{$sep}b{$sep}c", '', "a", 'b', "c", ''],
			["{$sep}", "{$sep}"],
			["abc", 'abc'],
			["abc{$sep}def{$sep}GHI", 'abc', 'def', 'ghi', '..', '.', 'GHI'],
			["{$sep}abc{$sep}def{$sep}GHI", "{$sep}abc", 'def', 'ghi', '..', '.', 'GHI'],
		];
	}

	#[DataProvider('provider_combine')]
	public function test_combine(string $expected, string $basePath, string ...$addPaths)
	{
		$actual = Path::combine($basePath, ...$addPaths);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getDirectoryPath()
	{
		return [
			[".", "name"],
			["path", "path/name"],
			["path1/path2", "path1/path2/name"],
			["/path1/path2", "/path1/path2/name"],
		];
	}

	#[DataProvider('provider_getDirectoryPath')]
	public function test_getDirectoryPath(string $expected, string $path)
	{
		$actual = Path::getDirectoryPath($path);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getFileName()
	{
		return [
			["name", "name"],
			["name", "path/name"],
			//["name", "path\\name"], // winだけ？
			["", "/"],
			[".", "/."],
			["b", "a/b"],
			[".", "./"],
			[".", "./."],
			["..", "../"],
			["a", "../../a"],
		];
	}

	#[DataProvider('provider_getFileName')]
	public function test_getFileName(string $expected, string $path)
	{
		$actual = Path::getFileName($path);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getFileExtension()
	{
		return [
			["", "", false],
			["", "  ", false],
			["", ".", false],
			["txt", "a.txt", false],
			["txt", "a.b.txt", false],
			["txt", ".txt", false],
			["", "txt", false],

			["", "", true],
			["", "  ", true],
			[".", ".", true],
			[".txt", "a.txt", true],
			[".txt", "a.b.txt", true],
			[".txt", ".txt", true],
			["", "txt", true],
		];
	}

	#[DataProvider('provider_getFileExtension')]
	public function test_getFileExtension(string $expected, string $path, bool $withDot = false)
	{
		$actual = Path::getFileExtension($path, $withDot);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getFileNameWithoutExtension()
	{
		return [
			["", ""],
			[" ", " "],
			["a", "a.b"],
			["a.b", "a.b.c"],
			["style", "style.css"],
			["style", "/dir/style.css"],
			["", ".htaccess"],
			["", "."],
			[".", ".."],
		];
	}

	#[DataProvider('provider_getFileNameWithoutExtension')]
	public function test_getFileNameWithoutExtension(string $expected, string $path)
	{
		$actual = Path::getFileNameWithoutExtension($path);
		$this->assertSame($expected, $actual);
	}

	public static function provider_toParts()
	{
		return [
			[new PathParts('/a', 'b.c', 'b', 'c'), '/a/b.c'],
			[new PathParts('.', 'a.b', 'a', 'b'), 'a.b'],
			[new PathParts('.', 'a', 'a', ''), 'a'],
			[new PathParts('.', '.htaccess', '', 'htaccess'), '.htaccess'],
			[new PathParts('/🍳/🚽', '💩.🚮', '💩', '🚮'), '/🍳/🚽/💩.🚮'],
		];
	}

	#[DataProvider('provider_toParts')]
	public function test_toParts(PathParts $expected, string $path)
	{
		$actual = Path::toParts($path);
		$this->assertSame($expected->directory, $actual->directory);
		$this->assertSame($expected->fileName, $actual->fileName);
		$this->assertSame($expected->fileNameWithoutExtension, $actual->fileNameWithoutExtension);
		$this->assertSame($expected->extension, $actual->extension);
	}

	public static function provider_setEnvironmentName()
	{
		return [
			['name.env.ext', 'name.ext', 'env'],
			['.debug.env', '.env', 'debug'],
			['name-only.debug', 'name-only', 'debug'],
			['name.name.<ENV>.ext', 'name.name.ext', '<ENV>'],
			['.' . DIRECTORY_SEPARATOR . 'name.env.ext', './name.ext', 'env'],
			['..' . DIRECTORY_SEPARATOR . 'name.env.ext', '../name.ext', 'env'],
			[DIRECTORY_SEPARATOR . 'name.env.ext', '/name.ext', 'env'],
			['C:\\name.env.ext', 'C:\\name.ext', 'env'],
			['C:\\dir' . DIRECTORY_SEPARATOR . 'name.env.ext', 'C:\\dir/name.ext', 'env'],
		];
	}

	#[DataProvider('provider_setEnvironmentName')]
	public function test_setEnvironmentName(string $expected, string $path, string $environment)
	{
		$actual = Path::setEnvironmentName($path, $environment);
		$this->assertSame($expected, $actual);
	}

	public static function provider_setEnvironmentName_throw()
	{
		return [
			['', 'env', '$path'],
			['  ', 'env', '$path'],
			['path', '', '$environment'],
			['path', '   ', '$environment'],
		];
	}

	#[DataProvider('provider_setEnvironmentName_throw')]
	public function test_setEnvironmentName_throw(mixed $path, mixed $environment, string $message)
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($message);
		Path::setEnvironmentName($path, $environment);
	}

	public function test_toParts_empty_throw()
	{
		$this->expectException(ArgumentException::class);
		Path::toParts('');
		$this->fail();
	}
}
