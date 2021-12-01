<?php declare(strict_types=1);
require_once('program/core/Route.php');

function getRouteMap(): array {
	$result = [
		new Route('', 'HomeController')
		,
		new Route('api/hello', 'api/HelloController')
		,
	];

	return $result;
}

