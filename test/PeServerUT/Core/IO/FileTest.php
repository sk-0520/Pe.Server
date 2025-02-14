<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\Cryptography;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\TestClass;
use Throwable;

class FileTest extends TestClass
{
	public function test_createEmptyFileIfNotExists()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertFalse(File::exists($path));
		File::createEmptyFileIfNotExists($path);
		$this->assertTrue(File::exists($path));
	}

	public function test_getFileSize()
	{
		$this->assertSame(File::getFileSize(__FILE__), File::getFileSize(__FILE__), __FILE__);
	}

	public function test_getFileSize_throw()
	{
		$this->expectException(IOException::class);
		File::getFileSize(__FILE__ . "\0" . '/');
		$this->fail();
	}

	public function test_readContent_writeContent()
	{
		$testDir = $this->testDir();
		$name = __FUNCTION__;
		$value = new Binary(__METHOD__);
		$path = $testDir->newPath($name);

		$this->assertCount(File::writeContent($path, $value), $value);
		$this->assertSame($value->raw, File::readContent($path)->raw);
	}

	public function test_readContent_throw()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->expectException(IOException::class);
		File::readContent($path);
	}

	public function test_writeContent()
	{
		$testDir = $this->testDir();
		$name = __FUNCTION__;
		$path = $testDir->createFile($name, new Binary('ABC'));

		$this->assertSame('ABC', File::readContent($path)->raw);

		File::writeContent($path, new Binary('Z'));
		$this->assertSame('Z', File::readContent($path)->raw);
	}

	public function test_writeContent_throw()
	{
		$testDir = $this->testDir();
		$name = "/"; //NOTE: いやまぁ、できるかもしれんけど
		$value = new Binary(__METHOD__);
		$path = $testDir->newPath($name);

		$this->expectException(IOException::class);
		File::writeContent($path, $value);
	}

	public function test_appendContent()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertFalse(File::exists($path));

		File::appendContent($path, new Binary(('abc')));
		$this->assertTrue(File::exists($path));
		$this->assertSame('abc', File::readContent($path)->raw);

		File::appendContent($path, new Binary(('def')));
		$this->assertSame('abcdef', File::readContent($path)->raw);
	}

	public function test_readJsonFile()
	{
		$dir = $this->testDir();
		$path = $dir->createFile(
			"a.json",
			new Binary('{ "int": 123, "string": "text" }')
		);
		$actual = File::readJsonFile($path);
		$this->assertSame(123, $actual["int"]);
		$this->assertSame("text", $actual["string"]);
	}

	public function test_JsonFile()
	{
		$dir = $this->testDir();

		$path = $dir->newPath("a.json");
		File::writeJsonFile($path, [ "int" => 123, "string" => "text" ]);

		$actual = File::readJsonFile($path);
		$this->assertSame(123, $actual["int"]);
		$this->assertSame("text", $actual["string"]);
	}


	public function test_exists()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->assertFalse(File::exists($path));
		File::createEmptyFileIfNotExists($path);
		$this->assertTrue(File::exists($path));
	}

	public function test_copy()
	{
		$testDir = $this->testDir();
		$src = $testDir->newPath(__FUNCTION__ . '.src');
		$dst = $testDir->newPath(__FUNCTION__ . '.dst');

		$this->assertFalse(File::exists($dst));
		File::writeContent($src, new Binary('abc'));
		$this->assertTrue(File::copy($src, $dst));
		$this->assertSame('abc', File::readContent($dst)->raw);

		$this->assertTrue(File::exists($dst));
		File::writeContent($src, new Binary('xyz'));
		$this->assertTrue(File::copy($src, $dst));
		$this->assertSame('xyz', File::readContent($dst)->raw);
	}

	public function test_removeFile()
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile(__FUNCTION__);

		File::removeFile($path);
		$this->assertFalse(File::exists($path));
	}

	public function test_removeFile_notFound_throw()
	{
		$testDir = $this->testDir();
		$path = $testDir->newPath(__FUNCTION__);

		$this->expectException(IOException::class);
		File::removeFile($path);
		$this->fail();
	}

	public function test_removeFile_dir_throw()
	{
		$testDir = $this->testDir();
		$path = $testDir->createDirectory(__FUNCTION__);

		$this->expectException(IOException::class);
		File::removeFile($path);
		$this->fail();
	}

	public function test_removeFileIfExists()
	{
		$testDir = $this->testDir();
		$file = $testDir->createFile(__FUNCTION__);
		$dir = $testDir->createDirectory(__FUNCTION__ . '-DIR');

		$this->assertTrue(File::removeFileIfExists($file));
		$this->assertFalse(File::exists($file));
		$this->assertFalse(File::removeFileIfExists($file));
		$this->assertFalse(File::removeFileIfExists($dir));
	}

	public function test_createUniqueFilePath_arg_throw()
	{
		$this->expectException(ArgumentException::class);
		File::createUniqueFilePath('', '');
		$this->fail();
	}
}
