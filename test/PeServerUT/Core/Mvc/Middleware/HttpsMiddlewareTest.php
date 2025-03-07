<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Middleware;

use PeServer\Core\Environment;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Mvc\Middleware\HttpsMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

class HttpsMiddlewareTest extends TestClass
{
	#region funcrion

	public function test_handleBefore_http()
	{
		$obj = new HttpsMiddleware(new NullLogger());
		$arg = new MiddlewareArgument(
			new RequestPath("", new UrlHelper("")),
			new Stores(
				$this->createMock(SpecialStore::class),
				StoreOptions::default(),
				$this->createMock(WebSecurity::class)
			),
			$this->createMock(Environment::class),
			$this->createMock(HttpRequest::class)
		);

		$actual = $obj->handleBefore($arg);
		$this->assertInstanceOf(MiddlewareResult::class, $actual);
	}

	public function test_handleBefore_https()
	{
		$mockSpecialStore = $this->createMock(SpecialStore::class);
		$mockSpecialStore ->method("isHttps")->willReturn(true);

		$obj = new HttpsMiddleware(new NullLogger());
		$arg = new MiddlewareArgument(
			new RequestPath("https://localhost.invalid", new UrlHelper("")),
			new Stores(
				$mockSpecialStore,
				StoreOptions::default(),
				$this->createMock(WebSecurity::class)
			),
			$this->createMock(Environment::class),
			$this->createMock(HttpRequest::class)
		);

		$actual = $obj->handleBefore($arg);
		$this->assertSame(MiddlewareResult::none(), $actual);
	}

	public function test_handleAfter()
	{
		$obj = new HttpsMiddleware(new NullLogger());
		$arg = new MiddlewareArgument(
			new RequestPath("", new UrlHelper("")),
			new Stores(
				$this->createMock(SpecialStore::class),
				StoreOptions::default(),
				$this->createMock(WebSecurity::class)
			),
			$this->createMock(Environment::class),
			$this->createMock(HttpRequest::class)
		);

		$actual = $obj->handleAfter($arg, $this->createMock(HttpResponse::class));

		$this->assertSame(MiddlewareResult::none(), $actual);
	}


	#endregion
}
