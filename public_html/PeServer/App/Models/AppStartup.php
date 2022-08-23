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
use PeServer\Core\ArrayUtility;
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

	protected function setupCommon(array $options, IDiRegisterContainer $container): void
	{
		parent::setupCommon($options, $container);

		/** @var ILogProvider */
		$logProvider = $container->get(ILogProvider::class);
		$appConfig = new AppConfiguration(
			$this->definedDirectory->public,
			$this->definedDirectory->application,
			ArrayUtility::getOr($options, 'url_helper', new UrlHelper('')), //@phpstan-ignore-line UrlHelper
			ArrayUtility::getOr($options, 'special_store', new SpecialStore()) //@phpstan-ignore-line SpecialStore
		);
		$container->registerValue($appConfig);

		$logging = $appConfig->setting["logging"];
		foreach ($logging as $name => $value) {
			if (is_array($value) && isset($value['logger_class']) && isset($value['configuration'])) {
				$logProvider->add($name, $value['logger_class'], $value['configuration']);
			}
		}

		$container->registerMapping(ITemplateFactory::class, AppTemplateFactory::class);
		$container->registerMapping(IDatabaseConnection::class, AppDatabaseConnection::class);
		$container->registerClass(AppCryptography::class);
		$container->registerClass(AppDatabaseCache::class);
		$container->registerMapping(Mailer::class, AppMailer::class);
		$container->registerClass(AppTemplate::class);
		$container->registerClass(AppArchiver::class);
	}

	protected function setupWebService(array $options, IDiRegisterContainer $container): void
	{
		parent::setupWebService($options, $container);

		/** @var AppConfiguration */
		$appConfig = $container->get(AppConfiguration::class);
		$container->registerValue($appConfig->stores);

		/** @var SpecialStore */
		$specialStore = $container->get(SpecialStore::class);

		$method = HttpMethod::from($specialStore->getServer('REQUEST_METHOD'));
		$requestPath = new RequestPath(
			$specialStore->getServer('REQUEST_URI'),
			ArrayUtility::getOr($options, 'url_helper', new UrlHelper('')), //@phpstan-ignore-line UrlHelper
		);
		$container->registerValue(new RouteRequest($method, $requestPath));

		$container->add(RouteSetting::class, DiItem::value($container->new(AppRouteSetting::class)));
		$container->registerMapping(Routing::class, AppRouting::class);

		/** @var AppErrorHandler */
		$appErrorHandler = $container->new(AppErrorHandler::class, [RequestPath::class => $requestPath]);
		$appErrorHandler->register();
	}
}