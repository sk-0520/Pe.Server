<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/PeServer/Core/AutoLoader.php');

use PeServer\Core\Routing;
use PeServer\Core\AutoLoader;
use PeServer\Core\Http\RequestPath;
use PeServer\App\Models\AppRouting;
use PeServer\App\Models\Initializer;
use PeServer\Core\Store\CookieOption;
use PeServer\App\Models\RouteConfiguration;
use PeServer\App\Models\StoreConfiguration;
use PeServer\Core\Http\HttpMethod;

ini_set('display_errors', '1');
error_reporting(E_ALL);

AutoLoader::initialize(
	[
		__DIR__,
	],
	'/^PeServer/'
);
Initializer::initialize(
	__DIR__,
	__DIR__ . '/PeServer',
	$_SERVER['SERVER_NAME'] === 'localhost' ? 'development' : 'production',
	':REVISION:'
);

$method = HttpMethod::from($_SERVER['REQUEST_METHOD']);
$requestPath = new RequestPath($_SERVER['REQUEST_URI'], '');
$routing = new AppRouting($method, $requestPath, RouteConfiguration::get(), StoreConfiguration::get());
$routing->execute();
