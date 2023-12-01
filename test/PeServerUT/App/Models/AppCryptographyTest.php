<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Models\AppCryptography;
use PeServerTest\Data;
use PeServerTest\TestClass;

class AppCryptographyTest extends TestClass
{
	public function test_enc_dec()
	{
		/** @var AppCryptography */
		$appCryptography = $this->container()->new(AppCryptography::class);

		$tests = [
			'a',
			'あ',
			'💩',
		];
		foreach ($tests as $test) {
			$data = $appCryptography->encrypt($test);
			$actual = $appCryptography->decrypt($data);
			$this->assertSame($test, $actual);
		}
	}

	public function test_token()
	{
		/** @var AppCryptography */
		$appCryptography = $this->container()->new(AppCryptography::class);

		$tests = [
			'a',
			'あ',
			'💩',
		];
		foreach ($tests as $test) {
			$data = $appCryptography->encryptToken($test);
			$actual = $appCryptography->decryptToken($data);
			$this->assertSame($test, $actual);
		}
	}

	public function test_convertMarker()
	{
		/** @var AppCryptography */
		$appCryptography = $this->container()->new(AppCryptography::class);

		// CryptoSetting::pepper が付与されることに注意
		$tests = [
			new Data(0x233643e7, 'abc'),
			new Data(0xb824c4a5, '🧶'),
		];
		foreach ($tests as $test) {
			$actual = $appCryptography->toMark(...$test->args);
			$this->assertSame($test->expected, $actual);
		}
	}
}
