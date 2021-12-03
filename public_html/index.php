<?php
require_once('PeServer/Config/RouteConfig.php');
require_once('PeServer/Core/Routing.php');

$routeMap = getRouteMap();
$routing = new Routing($routeMap, 'PeServer/App/Controllers');
$routing->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>

