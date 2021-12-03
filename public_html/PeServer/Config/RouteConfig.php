<?php declare(strict_types=1);
require_once('PeServer/Core/Route.php');

function getRouteMap(): array {
	$result = [
		new Route('', 'HomeController')
		,
		new Route('api/hello', 'Api/HelloController')
		,
	];

	return $result;
}

