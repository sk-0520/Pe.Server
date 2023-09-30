<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/PeServer/Core/AutoLoader.php');

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppRouteSetting;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\AppSpecialStore;
use PeServer\App\Models\AppStartup;
use PeServer\App\Models\Initializer;
use PeServer\Core\AutoLoader;
use PeServer\Core\DefinedDirectory;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\RouteRequest;
use PeServer\Core\Web\UrlHelper;

$autoLoader = new AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__ . DIRECTORY_SEPARATOR . 'PeServer',
		]
	]
);
$autoLoader->register(false);

$urlHelper = new UrlHelper('');
$specialStore = new AppSpecialStore();
$isLocalhost = $specialStore->getServerName() === 'localhost';

$appTestMode = '';
error_reporting(E_ALL);
if ($isLocalhost) {
	ini_set('display_errors', 'On');
	$mode = getenv('APP_TEST_MODE');
	if(is_string($mode)) {
		$appTestMode = $mode;
	}
} else {
	ini_set('display_errors', 'Off');
}

$startup = new AppStartup(
	new DefinedDirectory(
		__DIR__ . '/PeServer',
		__DIR__
	)
);
$container = $startup->setup(
	AppStartup::MODE_WEB,
	[
		'environment' => $isLocalhost ? ($appTestMode !== '' ? $appTestMode : 'development') : 'production',
		'revision' => ':REVISION:',
		'special_store' => $specialStore,
		'url_helper' => $urlHelper,
	]
);

/** @var IDiRegisterContainer */
$scope = $container->new(IDiRegisterContainer::class);

$routing = $scope->new(AppRouting::class); // new AppRouting($container->get(RouteRequest::class), new AppRouteSetting(), AppConfiguration::$stores);
$routing->execute();
