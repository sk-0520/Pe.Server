<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Cryptography;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\Data;
use PeServerTest\TestClass;
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

	public function test_clearCache_null()
	{
		IOUtility::clearCache(null);
		$this->success();
	}

	static function provider_clearCache_throw()
	{
		return [
			[''],
			[' '],
		];
	}
	/** @dataProvider provider_clearCache_throw */
	public function test_clearCache_throw($input)
	{
		$this->expectException(IOException::class);

		IOUtility::clearCache($input);
		$this->fail();
	}
}
