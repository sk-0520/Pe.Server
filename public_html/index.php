<?php
namespace PeServer;

require_once('PeServer/Core/AutoLoader.php');

use function \PeServer\Core\registerAutoLoader;

use \PeServer\Core\Routing;
use \PeServer\Config\RouteConfiguration;

registerAutoLoader([ __DIR__ ]);

require_once('PeServer/Config/RouteConfiguration.php');
require_once('PeServer/Core/Routing.php');

$routeConfiguration = new RouteConfiguration();
$routing = new Routing($routeConfiguration->get(), 'PeServer/App/Controllers');
$routing->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>

