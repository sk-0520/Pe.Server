<?php

declare(strict_types=1);

namespace PeServerUT\Core\Store;

use Error;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Store\TemporaryOptions;
use PeServerTest\TestClass;
use PeServer\Core\Throws\ArgumentException;

class TemporaryOptionsTest extends TestClass
{
	public function test_constructor()
	{
		$actual = new TemporaryOptions(
			'name',
			'savePath',
			new CookieOptions('/', null, false, true, 'lax')
		);

		$this->assertSame('name', $actual->name);
		$this->assertSame('savePath', $actual->savePath);
		$this->assertSame('/', $actual->cookie->path);
		$this->assertSame(false, $actual->cookie->secure);
		$this->assertSame(true, $actual->cookie->httpOnly);
		$this->assertSame('lax', $actual->cookie->sameSite);
	}

	public function test_constructor_throw()
	{
		$this->expectException(ArgumentException::class);
		new TemporaryOptions(
			' ',
			'savePath',
			new CookieOptions('/', null, false, true, 'lax')
		);
	}
}
