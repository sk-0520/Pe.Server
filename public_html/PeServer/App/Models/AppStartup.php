<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AppTemplateFactory;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\AppEraser;
use PeServer\Core\Collections\Arr;
use PeServer\Core\CoreStartup;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Log\ILogProvider;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Mvc\RouteSetting;
use PeServer\Core\Mvc\Routing;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServer\Core\Web\UrlHelper;

class AppStartup extends CoreStartup
{
	/**
	 * 初期化。
	 *
	 * @param DefinedDirectory $directory
	 */
	public function __construct(
		DefinedDirectory $directory
	) {
		parent::__construct($directory);
	}

	#region CoreStartup

	protected function setupCommon(array $options, IDiRegisterContainer $container): void
	{
		parent::setupCommon($options, $container);

		/** @var ILogProvider */
		$logProvider = $container->get(ILogProvider::class);
		$appConfig = new AppConfiguration(
			$this->definedDirectory->public,
			$this->definedDirectory->application,
			Arr::getOr($options, 'url_helper', new UrlHelper('')),
			Arr::getOr($options, 'special_store', new SpecialStore())
		);
		$container->registerValue($appConfig);

		$logging = $appConfig->setting->logging;
		foreach ($logging->loggers as $name => $value) {
			$logProvider->add($name, $value->loggerClass, $value->level, $value->format, $value->configuration);
		}

		$container->registerMapping(ITemplateFactory::class, AppTemplateFactory::class);
		$container->registerMapping(IDatabaseConnection::class, AppDatabaseConnection::class);
		$container->registerClass(AppCryptography::class);
		$container->registerClass(AppDatabaseCache::class);
		$container->registerMapping(Mailer::class, AppMailer::class);
		$container->registerClass(AppTemplate::class);
		$container->registerClass(AppArchiver::class);
		$container->registerClass(AppEraser::class);
	}

	protected function setupWebService(array $options, IDiRegisterContainer $container): void
	{
		parent::setupWebService($options, $container);

		/** @var AppConfiguration */
		$appConfig = $container->get(AppConfiguration::class);
		$container->registerValue($appConfig->stores);

		/** @var SpecialStore */
		$specialStore = $container->get(SpecialStore::class);

		$method = HttpMethod::from(Text::toUpper(Text::trim($specialStore->getServer('REQUEST_METHOD'))));
		$requestPath = new RequestPath(
			$specialStore->getServer('REQUEST_URI'),
			Arr::getOr($options, 'url_helper', new UrlHelper(''))
		);
		$container->registerValue(new RouteRequest($method, $requestPath));

		$container->add(RouteSetting::class, DiItem::value($container->new(AppRouteSetting::class)));
		$container->registerMapping(Routing::class, AppRouting::class);

		/** @var AppErrorHandler */
		$appErrorHandler = $container->new(AppErrorHandler::class, [RequestPath::class => $requestPath]);
		$appErrorHandler->register();
	}

	#endregion
}
