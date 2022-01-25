<?php

declare(strict_types=1);

namespace PeServerTest\App\Models;

use PeServer\App\Models\AppCryptography;
use PeServerTest\TestClass;

class AppCryptographyTest extends TestClass
{
	public function test_token()
	{
		$tests = [
			'a',
			'あ',
			'💩',
		];
		foreach ($tests as $test) {
			$data = AppCryptography::encryptToken($test);
			$actual = AppCryptography::decryptToken($data);
			$this->assertEquals($test, $actual);
		}
	}
}
