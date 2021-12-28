<?php

declare(strict_types=1);

namespace PeServer;

require_once(__DIR__ . '/PeServer/Core/AutoLoader.php');
require_once(__DIR__ . '/PeServer/Libs/smarty/libs/Smarty.class.php');

use \PeServer\Core\AutoLoader;
use \PeServer\Core\Routing;
use \PeServer\Core\Store\CookieOption;
use \PeServer\App\Models\RouteConfiguration;
use \PeServer\App\Models\Initializer;
use PeServer\App\Models\StoreConfiguration;

// ini_set('display_errors', '1');
// error_reporting( E_ALL );

AutoLoader::initialize([__DIR__], '/^PeServer/');
Initializer::initialize(
	__DIR__,
	__DIR__ . DIRECTORY_SEPARATOR . 'PeServer',
	$_SERVER['SERVER_NAME'] === 'localhost' ? 'development' : 'production',
	':REVISION:'
);

$routing = new Routing(RouteConfiguration::get(), StoreConfiguration::cookie());
$routing->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
