<?php

declare(strict_types=1);

namespace PeServerUT\App\Models;

use PeServer\App\Models\AppCryptography;
use PeServerTest\TestClass;

class AppCryptographyTest extends TestClass
{
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
}
