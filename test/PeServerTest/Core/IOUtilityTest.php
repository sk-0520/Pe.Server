<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\Cryptography;
use PeServer\Core\IOUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\Data;
use PeServerTest\TestClass;
use Throwable;

class IOUtilityTest extends TestClass
{

	function test_getFileSize()
	{
		$this->assertSame(IOUtility::getFileSize(__FILE__), IOUtility::getFileSize(__FILE__), __FILE__);
	}

	function test_getFileSize_throw()
	{
		$this->expectException(IOException::class);
		IOUtility::getFileSize(__FILE__ . "\0" . '/');
		$this->fail();
	}

	function test_createTemporaryFileStream()
	{
		try {
			$stream = IOUtility::createTemporaryFileStream();
			$stream->dispose();
			$this->success();
		} catch(Throwable $ex) {
			$this->fail($ex->__toString());
		}
	}
}
