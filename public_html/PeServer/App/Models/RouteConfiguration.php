<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\Route;
use \PeServer\App\Controllers\HomeController;
use \PeServer\App\Controllers\Api\HelloController;

class RouteConfiguration
{
	public static function get(): array
	{
		return [
			new Route('', HomeController::class),
			new Route('api/hello', HelloController::class),
		];
	}
}
