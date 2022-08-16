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

ini_set('display_errors', '1');
error_reporting(E_ALL);

$autoLoader = new AutoLoader(
	[
		'PeServer' => [
			'directory' => __DIR__,
		]
	]
);
$autoLoader->register(false);

$urlHelper = new UrlHelper('');
$specialStore = new AppSpecialStore();

$startup = new AppStartup(
	new DefinedDirectory(
		__DIR__ . '/PeServer',
		__DIR__
	)
);
$container = $startup->setup(
	AppStartup::MODE_WEB,
	[
		'environment' => $specialStore->getServer('SERVER_NAME') === 'localhost' ? 'development' : 'production',
		'revision' => ':REVISION:',
		'special_store' => $specialStore,
		'url_helper' => $urlHelper,
	]
);

/** @var IDiRegisterContainer */
$scope = $container->new(IDiRegisterContainer::class);

$routing = $scope->new(AppRouting::class); // new AppRouting($container->get(RouteRequest::class), new AppRouteSetting(), AppConfiguration::$stores);
$routing->execute();
