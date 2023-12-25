<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Cryptography;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Text;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\IOException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

class IOUtilityTest extends TestClass
{
	public function test_getState()
	{
		$state = IOUtility::getState(__FILE__);
		$this->success();
	}

	public function test_getState_throw()
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

	public static function provider_clearCache_throw()
	{
		return [
			[''],
			[' '],
		];
	}
	#[DataProvider('provider_clearCache_throw')]
	public function test_clearCache_throw($input)
	{
		$this->expectException(IOException::class);

		IOUtility::clearCache($input);
		$this->fail();
	}

	public function test_move_file()
	{
		$testDir = $this->testDir();
		$src = $testDir->createFile(__FUNCTION__);
		$dst = $src . '.dst';

		IOUtility::move($src, $dst);
		$this->assertFalse(File::exists($src));
		$this->assertTrue(File::exists($dst));
	}
}
