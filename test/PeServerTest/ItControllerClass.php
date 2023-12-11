<?php

declare(strict_types=1);

namespace PeServerTest;

use Error;
use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppStartup;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\Data\SessionAnonymous;
use PeServer\App\Models\Domain\UserState;
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
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Html\HtmlNodeBase;
use PeServer\Core\Html\HtmlTextElement;
use PeServer\Core\IO\File;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\DiContainerArgumentException;
use PeServer\Core\Throws\RegexException;
use PeServer\Core\Throws\DiContainerException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\OutputBufferException;
use PeServer\Core\Web\UrlPath;
use PeServerTest\TestClass;
use PeServerTest\ItSpecialStore as ItSpecialStore;
use PeServerTest\ItActual;
use PeServerTest\TestRouting;
use PeServerTest\ItRoutingWithoutMiddleware;
use PeServerTest\ItSetup;
use Reflection;
use ReflectionClass;
use TypeError;
use Throwable;
use ValueError;

class ItControllerClass extends TestClass
{
	#region property
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

	/**
	 *
	 * @param HttpMethod $httpMethod
	 * @param string $path
	 * @param ItOptions $options
	 * @param null|callable(ItSetup) $setup
	 * @return ItActual
	 */
	protected function call(HttpMethod $httpMethod, string $path, ItOptions $options = new ItOptions(), ?callable $setup = null): ItActual
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
				'environment' => 'it',
				'revision' => ':REVISION:',
				'special_store' => new ItSpecialStore($httpMethod, $path, $options->httpHeader, $options->body),
				'url_helper' => new UrlHelper(''),
			]
		);

		if ($options->stores->account instanceof SessionAccount || $options->stores->account instanceof SessionAnonymous) {
			/** @var AppConfiguration */
			$config = $container->get(AppConfiguration::class);
			/** @var CookieStore */
			$cookie = $container->get(CookieStore::class);
			/** @var SessionStore */
			$session = $container->get(SessionStore::class);

			$cookie->set($config->setting->store->session->name, session_id());

			if ($options->stores->account instanceof SessionAccount) {
				$session->set(SessionKey::ACCOUNT, $options->stores->account);
			} else {
				assert($options->stores->account instanceof SessionAnonymous);
				$session->set(SessionKey::ANONYMOUS, $options->stores->account);
			}
		}

		$container->remove(ResponsePrinter::class);
		$container->add(ResponsePrinter::class, DiItem::class(ItResponsePrinter::class));

		$useDatabase = 'useDatabase';
		if (isset($this->$useDatabase) && $this->$useDatabase) {
			$this->resetDatabase($container);

			if ($setup) {
				/** @var IDatabaseConnection */
				$databaseConnection = $container->get(IDatabaseConnection::class);
				$database = $databaseConnection->open();
				$database->transaction(function (IDatabaseContext $context) use ($setup, $options, $container) {
					$setup(new ItSetup($container, $context));

					if (!$options->stores->enabledSetupUser) {
						$usersEntityDao = new UsersEntityDao($context);
						$usersEntityDao->updateUserState(
							'00000000-0000-4000-0000-000000000000',
							UserState::DISABLED
						);
					}

					return true;
				});
			}
		}

		/** @var ItRoutingWithoutMiddleware */
		$routing = $container->new(ItRoutingWithoutMiddleware::class);
		$routing->execute();

		$response = ItResponsePrinter::getResponse();
		if ($response === null) {
			throw new Error("例外とかミドルウェア系の失敗を取る術がないのです。かなしい");
		}

		return new ItActual($response, $container);
	}

	protected function assertStatus(HttpStatus $expected, ItActual $response): void
	{
		$this->assertSame($expected, $response->getHttpStatus());
	}

	protected function assertStatusOk(ItActual $response): void
	{
		$this->assertStatus(HttpStatus::OK, $response);
	}

	//TODO: とりまつくっとくのです（リダイレクト周りのテストが死んでる）
	protected function assertRedirectPath(HttpStatus $status, UrlPath|string $path, UrlQuery|null $query, ItActual $response): void
	{
		$this->assertTrue($status->isRedirect());

		$this->assertStatus($status, $response);

		if (is_string($path)) {
			$path = new UrlPath($path);
		}

		$this->assertSame((string)$path, (string)$response->response->header->getRedirect()->url->path);
	}

	protected function assertMime(string $mime, ItActual $response): void
	{
		$this->assertSame($mime, $response->getContentType()->mime);
	}


	protected function assertTitle(string $expected, ItActual $response): void
	{
		$this->assertTrue($response->isHtml());
		$this->assertSame($expected . ' - Peサーバー', $response->html->getTitle());
	}

	protected function assertTextNode(string $expected, HtmlNodeBase $node): void
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

	protected function assertAttribute(string $expected, HtmlTagElement $element, string $attributeName): void
	{
		$attributeValue = $element->getAttribute($attributeName);
		$this->assertSame($expected, $attributeValue, $attributeValue);
	}

	protected function assertValue(string $expected, HtmlTagElement $element): void
	{
		if ($element->raw->tagName === 'input') {
			$this->assertAttribute($expected, $element, 'value');
		} else {
			assert($element->raw->tagName === 'textarea');
			$this->assertTextNode($expected, $element);
		}
	}

	protected function assertVisibleCommonError(array $errorItems, ItActual $response)
	{
		$root = $response->html->path()->collections(
			"//main/div[contains(@class, 'common') and contains(@class, 'error')]"
		);
		$this->assertCount(1, $root);

		$targetElements = $response->html->path()->collections(
			"//main/div[contains(@class, 'common') and contains(@class, 'error')]//li[contains(@class, 'error')]"
		)->toArray();
		$this->assertSame(count($targetElements), count($errorItems));
		for ($i = 0; $i < count($targetElements); $i++) {
			$this->assertTextNode($errorItems[$i], $targetElements[$i]);
		}
	}

	protected function assertVisibleTargetError(array $errorItems, string $name, ItActual $response)
	{
		$targetElements = $response->html->path()->collections(
			"//main//form//*[@name='$name']//following-sibling::ul[contains(@class, 'value-error')]//li[contains(@class, 'error')]"
		)->toArray();
		$this->assertCount(count($errorItems), $targetElements);
		for ($i = 0; $i < count($targetElements); $i++) {
			$this->assertTextNode($errorItems[$i], $targetElements[$i]);
		}
	}

	#endregion
}