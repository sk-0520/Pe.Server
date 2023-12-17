<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Collections\Arr;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Encoding;
use PeServer\Core\Environment;
use PeServer\Core\ErrorHandler;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Http\ResponsePrinter;
use PeServer\Core\Http\ResponsePrinterFactory;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\ILogProvider;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\LogProvider;
use PeServer\Core\Mvc\ILogicFactory;
use PeServer\Core\Mvc\LogicFactory;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\WebSecurity;


/**
 * スタートアップ処理。
 *
 * これだけでも動くけど基本的に書き換えてあれこれする想定。
 */
class CoreStartup
{
	#region define

	public const MODE_WEB = 'Web';
	public const MODE_CLI = 'Cli';
	public const MODE_TEST = 'Test';

	#endregion

	/**
	 * 生成。
	 *
	 * @param DefinedDirectory $definedDirectory ディレクトリ定義。
	 */
	public function __construct(
		protected DefinedDirectory $definedDirectory
	) {
	}

	#region function

	/**
	 * 共通セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCommon(array $options, IDiRegisterContainer $container): void
	{
		$environment = new Environment('C', 'uni', 'Asia/Tokyo', $options['environment'], $options['revision']);
		Encoding::setDefaultEncoding(Encoding::getUtf8());

		$logging = new Logging(Arr::getOr($options, 'special_store', new SpecialStore()));
		$container->registerValue($logging, Logging::class);

		$container->registerValue($environment, Environment::class);
		$container->registerValue($this->definedDirectory, DefinedDirectory::class);

		$container->registerClass(WebSecurity::class);

		$container->registerMapping(ILogProvider::class, LogProvider::class, DiItem::LIFECYCLE_SINGLETON);
		$container->registerMapping(ILoggerFactory::class, LoggerFactory::class);
		$container->add(ILogger::class, DiItem::factory(Logging::class . '::injectILogger'));
		// $container->add(ILogger::class, DiItem::factory(function (IDiContainer $container, array $callStack) {
		// 	/** @var DiItem[] $callStack */
		// 	$loggerFactory = $container->get(ILoggerFactory::class);
		// 	if (/*1 < */count($callStack)) {
		// 		//$item = $callStack[count($callStack) - 2];
		// 		$item = $callStack[0];
		// 		$className = (string)$item->data; // あぶねぇかなぁ
		// 		$header = Logging::toHeader($className);
		// 		return $loggerFactory->create($header);
		// 	}
		// 	return $loggerFactory->create('<UNKNOWN>');
		// }));

		$container->add(IDiContainer::class, new DiItem(DiItem::LIFECYCLE_SINGLETON, DiItem::TYPE_VALUE, $container, true));
		$container->registerMapping(ITemplateFactory::class, TemplateFactory::class);
		$container->registerClass(TemplateFactory::class); // こいつは Core からも使われる特殊な奴やねん
		$container->registerMapping(IResponsePrinterFactory::class, ResponsePrinterFactory::class); // こいつも Core からも使われる特殊な奴やねん
		//$container->registerClass(ResponsePrinterFactory::class);

		// Core のセットアップ時点で死ぬようではもういいです
		if (!$environment->isTest()) {
			$errorHandler = $container->new(ErrorHandler::class);
			$errorHandler->register();
		}
	}

	/**
	 * Webアプリケーション用セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupWebService(array $options, IDiRegisterContainer $container): void
	{
		$container->add(IDiRegisterContainer::class, DiItem::factory(fn ($dc) => $dc->clone()));
		$container->remove(IDiContainer::class);
		$container->add(IDiContainer::class, DiItem::factory(fn ($dc) => $dc->get(IDiRegisterContainer::class)));

		$container->registerValue(Arr::getOr($options, 'url_helper', new UrlHelper('')), IUrlHelper::class);

		/** @var SpecialStore */
		$specialStore = Arr::getOr($options, 'special_store', new SpecialStore());
		$container->registerValue($specialStore, SpecialStore::class);
		$container->add(Stores::class, DiItem::factory(fn ($di) => new Stores($di->get(SpecialStore::class), StoreOptions::default(), $di->get(WebSecurity::class)), DiItem::LIFECYCLE_SINGLETON));
		$container->add(CookieStore::class, DiItem::factory(fn ($di) => $di->get(Stores::class)->cookie));
		$container->add(SessionStore::class, DiItem::factory(fn ($di) => $di->get(Stores::class)->session));
		$container->add(TemporaryStore::class, DiItem::factory(fn ($di) => $di->get(Stores::class)->temporary));


		$method = $specialStore->getRequestMethod();
		$requestPath = new RequestPath($specialStore->getServer('REQUEST_URI'), $container->get(IUrlHelper::class));
		$container->registerValue(new RouteRequest($method, $requestPath));

		$container->registerMapping(ILogicFactory::class, LogicFactory::class);
	}

	/**
	 * CLIアプリケーション用セットアップ処理。
	 *
	 * つかわんよ。
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCliService(array $options, IDiRegisterContainer $container): void
	{
		//NOP
	}

	/**
	 * テスト用セットアップ処理。
	 *
	 * 拡張する場合は先に親を呼び出して、子の方で登録(再登録)を行うこと。
	 *
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupTestService(array $options, IDiRegisterContainer $container): void
	{
		//NOP
	}

	/**
	 * 追加セットアップ処理。
	 *
	 * Coreでは何もしないので拡張側で好きにどうぞ。
	 *
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @param IDiRegisterContainer $container
	 */
	protected function setupCustom(array $options, IDiRegisterContainer $container): void
	{
		//NOP
	}

	/**
	 * セットアップ処理。
	 *
	 * @param string $mode
	 * @param array{environment:string,revision:string,url_helper?:IUrlHelper,special_store?:SpecialStore} $options
	 * @return IDiRegisterContainer
	 */
	public function setup(string $mode, array $options): IDiRegisterContainer
	{
		$container = new DiRegisterContainer();

		$this->setupCommon($options, $container);

		switch ($mode) {
			case self::MODE_WEB:
				$this->setupWebService($options, $container);
				break;

			case self::MODE_CLI:
				$this->setupCliService($options, $container);
				break;

			case self::MODE_TEST:
				$this->setupTestService($options, $container);
				break;

			default:
				throw new NotImplementedException($mode);
		}

		$this->setupCustom($options, $container);

		return $container;
	}

	#endregion
}
