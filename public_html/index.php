<?php

namespace PeServer;

require_once('PeServer/Core/AutoLoader.php');
require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \PeServer\Core\AutoLoader;
use \PeServer\Core\Routing;
use \PeServer\App\Models\RouteConfiguration;
use \PeServer\App\Models\Initializer;

AutoLoader::initialize([__DIR__], '/^PeServer/');
Initializer::initialize(__DIR__, __DIR__ . DIRECTORY_SEPARATOR . 'PeServer', $_SERVER['SERVER_NAME'] === 'localhost' ? 'development' : 'production');

$routeConfiguration = new RouteConfiguration();
$routing = new Routing($routeConfiguration->get());
$routing->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
