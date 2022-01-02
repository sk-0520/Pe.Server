<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/PeServer/Core/AutoLoader.php');

use PeServer\Core\Routing;
use PeServer\Core\AutoLoader;
use PeServer\Core\RequestPath;
use PeServer\App\Models\Initializer;
use PeServer\Core\Store\CookieOption;
use PeServer\App\Models\RouteConfiguration;
use PeServer\App\Models\StoreConfiguration;

ini_set('display_errors', '1');
error_reporting( E_ALL );

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

$routing = new Routing(RouteConfiguration::get(), StoreConfiguration::get());
$routing->execute($_SERVER['REQUEST_METHOD'], new RequestPath($_SERVER['REQUEST_URI'], ''));
