<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Cryptography;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerUT\Data;
use PeServerUT\TestClass;
use Throwable;

class IOUtilityTest extends TestClass
{
	function test_getState()
	{
		$state = IOUtility::getState(__FILE__);
		$this->success();
	}
	function test_getState_throw()
	{
		$this->expectException(IOException::class);
		IOUtility::getState(__FILE__ . "\0");
		$this->fail();
	}
}
