<?php
require_once('program/config/route-map.php');
require_once('program/core/routing.php');

$routeMap = getRouteMap();
$routing = new Routing($routeMap, 'program/app/controllers');
$routing->execute($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);


	$x = 1;
?>

PHP: <?php echo $x ?>

