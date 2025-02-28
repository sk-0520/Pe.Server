<?php

declare(strict_types=1);

namespace PeServerUT\Core\Store;

use Error;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Store\SessionOptions;
use PeServerTest\TestClass;
use PeServer\Core\Throws\ArgumentException;

class SessionOptionsTest extends TestClass
{
	public function test_constructor_throw()
	{
		$this->expectException(ArgumentException::class);
		new SessionOptions(
			' ',
			'savePath',
			"file",
			new CookieOptions('/', null, false, true, 'lax')
		);
	}
}
