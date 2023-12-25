<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Models\AppCryptography;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

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

	public static function provider_convertMarker()
	{
		return [
			[0x233643e7, 'abc'],
			[0xb824c4a5, '🧶'],
		];
	}

	#[DataProvider('provider_convertMarker')]
	public function test_convertMarker($expected, string $data)
	{
		/** @var AppCryptography */
		$appCryptography = $this->container()->new(AppCryptography::class);

		// CryptoSetting::pepper が付与されることに注意
		$actual = $appCryptography->toMark($data);
		$this->assertSame($expected, $actual);
	}
}
