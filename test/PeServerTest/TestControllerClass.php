<?php

declare(strict_types=1);

namespace PeServerTest;

use Error;
use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppStartup;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\App\Models\Setup\SetupRunner;
use PeServer\Core\Binary;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\DatabaseUtility;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiContainer;
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
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\UrlQuery;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Html\HtmlTextElement;
use PeServer\Core\IO\File;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Text;
use PeServerTest\TestClass;
use PeServerTest\TestDynamicSpecialStore;
use PeServerTest\TestHttpResponse;
use PeServerTest\TestRouting;
use PeServerTest\TestRoutingWithoutMiddleware;
use Reflection;
use ReflectionClass;

class TestControllerClass extends TestClass
{
	#region property

	protected IDiContainer $itContainer;

	#endregion

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

	protected function resetDatabase(IDiRegisterContainer $container): void
	{
		/** @var IDatabaseConnection */
		$databaseConnection = $container->get(IDatabaseConnection::class);

		$connectionSetting = $databaseConnection->getConnectionSetting();
		if (DatabaseUtility::isSqliteMemoryMode($connectionSetting)) {
			$databaseContext = $databaseConnection->open();

			$container->remove(IDatabaseConnection::class);
			$container->add(IDatabaseConnection::class, DiItem::factory(function () use ($connectionSetting, $databaseContext) {
				return new class($connectionSetting, $databaseContext) implements IDatabaseConnection
				{
					public function __construct(private ConnectionSetting $connectionSetting, private DatabaseContext $databaseContext)
					{
						//NOP
					}

					public function getConnectionSetting(): ConnectionSetting
					{
						return $this->connectionSetting;
					}
					public function open(): DatabaseContext
					{
						return $this->databaseContext;
					}
				};
			}), DiItem::LIFECYCLE_SINGLETON);

			/** @var IDatabaseConnection */
			$databaseConnection = $container->get(IDatabaseConnection::class);
		} else {
			$filePath = DatabaseUtility::getSqliteFilePath($connectionSetting);
			if (File::exists($filePath)) {
				File::removeFile($filePath);
			}
		}

		$setupRunner = new SetupRunner(
			$databaseConnection,
			$container->get(AppConfiguration::class),
			//$container->get(ILoggerFactory::class),
			new class implements ILoggerFactory
			{
				public function createLogger(string|object $header, int $baseTraceIndex = 0): ILogger
				{
					return new NullLogger();
				}
			}
		);

		$setupRunner->execute();
	}

	protected function call(HttpMethod $httpMethod, string $path, MockStores $stores = new MockStores(), ?callable $setup = null, ?HttpHeader $httpHeader = null, ?array $body = null): TestHttpResponse
	{
		$this->resetInitialize();

		$startup = new AppStartup(
			new DefinedDirectory(
				__DIR__ . '/../../public_html/PeServer',
				__DIR__ . '/../../public_html'
			)
		);

		$this->itContainer = $container = $startup->setup(
			AppStartup::MODE_WEB,
			[
				'environment' => 'it',
				'revision' => ':REVISION:',
				'special_store' => new TestDynamicSpecialStore($httpMethod, $path, $httpHeader, $body),
				'url_helper' => new UrlHelper(''),
			]
		);

		if ($stores->account instanceof SessionAccount) {
			/** @var AppConfiguration */
			$config = $container->get(AppConfiguration::class);
			/** @var CookieStore */
			$cookie = $container->get(CookieStore::class);
			/** @var SessionStore */
			$session = $container->get(SessionStore::class);

			$cookie->set($config->setting->store->session->name, session_id());
			$session->set(SessionKey::ACCOUNT, $stores->account);
		}

		$container->remove(ResponsePrinter::class);
		$container->add(ResponsePrinter::class, DiItem::class(TestResponsePrinter::class));

		$useDatabase = 'useDatabase';
		if (isset($this->$useDatabase) && $this->$useDatabase) {
			$this->resetDatabase($container);
		}

		if ($setup) {
			/** @var IDatabaseConnection */
			$databaseConnection = $container->get(IDatabaseConnection::class);
			$database = $databaseConnection->open();
			$database->transaction(function (IDatabaseContext $context) use ($setup, $container) {
				$setup($container, $context);
				return true;
			});
		}

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

	protected function assertTitle(string $expected, TestHttpResponse $response): void
	{
		$this->assertTrue($response->isHtml());
		$this->assertSame($expected . ' - Peサーバー', $response->html->getTitle());
	}

	protected function assertTextElement(string $expected, HtmlNodeBase $node): void
	{

		if ($node instanceof HtmlTextElement) {
			$actual = Text::trim($node->get());
			$this->assertSame($expected, $actual);
		} elseif ($node instanceof HtmlTagElement) {
			$actual = Text::trim($node->raw->textContent);
			$this->assertSame($expected, $actual);
		} else {
			$this->fail();
		}
	}

	#endregion
}
