<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use Exception;
use PeServer\App\Cli\Daily\DailyParameter;
use PeServer\App\Cli\HealthCheck\HealthCheckParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AppEmailInformation;
use PeServer\App\Models\AppErrorHandler;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\AppRoute;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AppTemplateFactory;
use PeServer\App\Models\AppUrl;
use PeServer\App\Models\Domain\AccessLogManager;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\AppEraser;
use PeServer\App\Models\Migration\AppMigrationRunnerFactory;
use PeServer\Core\Cli\CommandLine;
use PeServer\Core\Cli\LongOptionKey;
use PeServer\Core\Cli\ParameterKind;
use PeServer\Core\Collections\Arr;
use PeServer\Core\ProgramContext;
use PeServer\Core\CoreStartup;
use PeServer\Core\CoreStartupOption;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\StartupOptions;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Environment;
use PeServer\Core\Errors\HttpErrorHandler;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ConsoleLogger;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILogProvider;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mvc\Routing\Route;
use PeServer\Core\Mvc\Routing\RouteRequest;
use PeServer\Core\Mvc\Routing\RouteSetting;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Serialization\Mapper;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\UrlHelper;
use stdClass;

class AppStartup extends CoreStartup
{
	/**
	 * 初期化。
	 *
	 * @param StartupOptions $startupOptions
	 */
	public function __construct(
		StartupOptions $startupOptions,
	) {
		parent::__construct($startupOptions);
	}

	#region CoreStartup

	/**
	 *
	 * @param string $mode
	 * @param CoreStartupOption $options
	 * @param IDiRegisterContainer $container
	 */
	protected function registerErrorHandler(string $mode, CoreStartupOption $options, IDiRegisterContainer $container): void
	{
		if ($mode !== self::MODE_WEB) {
			return;
		}

		if ($this->startupOptions->errorHandling) {
			$specialStore = $options->specialStore ?? throw new Exception();
			//$method = $specialStore->getRequestMethod();
			$requestPath = new RequestPath($specialStore->getServer('REQUEST_URI'), $container->get(IUrlHelper::class));
			//$container->registerValue(new RouteRequest($method, $requestPath));

			$errorHandler = $container->new(AppErrorHandler::class, [RequestPath::class => $requestPath]);
			$errorHandler->register();
		}
	}

	protected function setupCommon(CoreStartupOption $options, IDiRegisterContainer $container): void
	{
		parent::setupCommon($options, $container);

		/** @var ILogProvider */
		$logProvider = $container->get(ILogProvider::class);

		$appConfig = new AppConfiguration(
			$container->get(ProgramContext::class),
			$options->urlHelper ?? new UrlHelper(''),
			$container->get(WebSecurity::class),
			$options->specialStore ?? new SpecialStore(),
			$container->get(Environment::class)
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
		$container->registerClass(AppEmailInformation::class);
		$container->registerClass(AppUrl::class);
		$container->registerClass(AccessLogManager::class);
		$container->registerClass(AppTemporary::class);
		$container->registerClass(AppMigrationRunnerFactory::class);
	}

	protected function setupWebService(CoreStartupOption $options, IDiRegisterContainer $container): void
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
			$options->urlHelper ?? new UrlHelper('')
		);
		$container->registerValue(new RouteRequest($method, $requestPath));

		$container->add(RouteSetting::class, DiItem::value($container->new(AppRouteSetting::class)));
		$container->registerMapping(Route::class, AppRoute::class);
	}

	protected function setupCliService(CoreStartupOption $options, IDiRegisterContainer $container): void
	{
		parent::setupCliService($options, $container);

		$container->registerValue(new SpecialStore(), SpecialStore::class);

		/** @var ILogProvider */
		$logProvider = $container->get(ILogProvider::class);
		$logProvider->clear("console");
		$logProvider->add(
			"console",
			ConsoleLogger::class,
			ILogger::LOG_LEVEL_INFORMATION,
			ConsoleLogger::FORMAT,
			[]
		);

		$container->add(
			HealthCheckParameter::class,
			new DiItem(
				DiItem::LIFECYCLE_SINGLETON,
				DiItem::TYPE_FACTORY,
				function ($di) {
					$options = new CommandLine([
						new LongOptionKey("echo", ParameterKind::NeedValue),
					]);
					$parsedResult = $options->parseArgv();
					$result = new HealthCheckParameter();
					$mapper = new Mapper();
					$mapper->mapping($parsedResult, $result);
					return $result;
				}
			)
		);

		$container->add(
			DailyParameter::class,
			new DiItem(
				DiItem::LIFECYCLE_SINGLETON,
				DiItem::TYPE_FACTORY,
				function ($di) {
					// $options = new CommandLine([
					// 	new LongOptionKey("echo", ParameterKind::NeedValue),
					// ]);
					// $parsedResult = $options->parseArgv();
					// $result = new HealthCheckParameter();
					// $mapper = new Mapper();
					// $mapper->mapping($parsedResult, $result);
					// return $result;
					return new DailyParameter();
				}
			)
		);
	}

	#endregion
}
