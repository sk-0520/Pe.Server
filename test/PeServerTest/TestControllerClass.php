<?php

declare(strict_types=1);

namespace PeServerTest;

use Error;
use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppStartup;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\I18n;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\Result\IActionResult;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\UrlQuery;
use PeServerTest\TestClass;
use PeServerTest\TestDynamicSpecialStore;
use PeServerTest\TestHttpResponse;
use PeServerTest\TestRouting;
use PeServerTest\TestRoutingWithoutMiddleware;
use Reflection;
use ReflectionClass;

class TestControllerClass extends TestClass
{
	#region function

	private function resetInitialize()
	{
		$classNames = [
			I18n::class,
		];

		foreach ($classNames as $className) {
			$dirty = new ReflectionClass($className);
			$dirty->setStaticPropertyValue('initializeChecker', null);
		}
	}

	protected function call(HttpMethod $httpMethod, string $path, MockStores $stores = new MockStores(), ?HttpHeader $httpHeader = null, ?array $body = null): TestHttpResponse
	{
		$this->resetInitialize();

		$startup = new AppStartup(
			new DefinedDirectory(
				__DIR__ . '/../../public_html/PeServer',
				__DIR__ . '/../../public_html'
			)
		);
		$container = $startup->setup(
			AppStartup::MODE_WEB,
			[
				'environment' => 'test',
				'revision' => ':REVISION:',
				'special_store' => new TestDynamicSpecialStore($httpMethod, $path, $httpHeader, $body),
				'url_helper' => new UrlHelper(''),
			]
		);

		$container->remove(ResponsePrinter::class);
		$container->add(ResponsePrinter::class, DiItem::class(TestResponsePrinter::class));

		/** @var TestRoutingWithoutMiddleware */
		$routing = $container->new(TestRoutingWithoutMiddleware::class);
		$routing->execute();

		$response = TestResponsePrinter::getResponse();
		if ($response === null) {
			throw new Error("例外とかミドルウェア系の失敗を取る術がないのです。かなしい");
		}

		return new TestHttpResponse($response);
	}

	protected function assertStatus(HttpStatus $expected, HttpMethod $httpMethod, string $path, MockStores $stores = new MockStores(), ?HttpHeader $httpHeader = null, ?array $body = null): void
	{
		try {
			$response = $this->call($httpMethod, $path, $stores, $httpHeader, $body);
			$this->assertSame($expected, $response->getHttpStatus());
		} catch (HttpStatusException $ex) {
			$this->assertSame($expected, $ex->status);
		}
	}


	#endregion
}
