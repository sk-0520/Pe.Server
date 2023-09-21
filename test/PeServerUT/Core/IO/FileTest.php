<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Cryptography;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\Data;
use PeServerTest\TestClass;
use Throwable;

class FileTest extends TestClass
{
	function test_getFileSize()
	{
		$this->assertSame(File::getFileSize(__FILE__), File::getFileSize(__FILE__), __FILE__);
	}

	function test_getFileSize_throw()
	{
		$this->expectException(IOException::class);
		File::getFileSize(__FILE__ . "\0" . '/');
		$this->fail();
	}

	function test_createTemporaryFileStream()
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
