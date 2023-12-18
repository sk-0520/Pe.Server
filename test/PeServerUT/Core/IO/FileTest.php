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
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\Data;
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

	public function test_writeContent_throw()
	{
		$testDir = $this->testDir();
		$name = "/"; //NOTE: いやまぁ、できるかもしれんけど
		$value = new Binary(__METHOD__);
		$path = $testDir->newPath($name);

		$this->expectException(IOException::class);
		File::writeContent($path, $value);
	}

	public function test_createTemporaryFileStream()
	{
		try {
			$stream = File::createTemporaryFileStream();
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->__toString());
		}
	}
}
