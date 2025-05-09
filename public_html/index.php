<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/../PeServer/Core/AutoLoader.php');

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\AppRoute;
use PeServer\App\Models\AppSpecialStore;
use PeServer\App\Models\AppStartup;
use PeServer\App\Models\AppStartupOption;
use PeServer\App\Models\Initializer;
use PeServer\Core\AutoLoader;
use PeServer\Core\CoreStartupOption;
use PeServer\Core\StartupOptions;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Web\UrlHelper;

$autoLoader = new AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'PeServer',
		]
	]
);
$autoLoader->register(false);

$urlHelper = new UrlHelper('');
$specialStore = new AppSpecialStore();
$isLocalhost = $specialStore->isLocalhost();

$appTestMode = '';
error_reporting(E_ALL);
if ($isLocalhost) {
	ini_set('display_errors', 'On');
	$mode = getenv('APP_TEST_MODE');
	if (is_string($mode)) {
		$appTestMode = $mode;
	}
} else {
	ini_set('display_errors', 'Off');
}

$startup = new AppStartup(
	new StartupOptions(
		root: __DIR__ . '/..',
		public: 'public_html'
	)
);
$container = $startup->setup(
	AppStartup::MODE_WEB,
	new AppStartupOption(
		$isLocalhost ? ($appTestMode !== '' ? $appTestMode : 'development') : 'production',
		':REVISION:',
		$specialStore
	)
);

/** @var IDiRegisterContainer */
$scope = $container->new(IDiRegisterContainer::class);

$route = $scope->new(AppRoute::class); // new AppRouting($container->get(RouteRequest::class), new AppRouteSetting(), AppConfiguration::$stores);
$route->execute();
