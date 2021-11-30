<?php
require_once('program/core/route.php');

function getRouteMap(): array {
	$result = [
		new Route('', 'HomeController')
		,
		new Route('api/hello', 'api/HelloController')
		,
	];

	return $result;
}

