<?php

declare(strict_types=1);

namespace PeServer\Config;

use \PeServer\Core\ConfigurationBase;
use \PeServer\Core\Route;
use \PeServer\App\Controllers\HomeController;
use \PeServer\App\Controllers\Api\HelloController;

final class RouteConfiguration extends ConfigurationBase
{
	function get(): array
	{
		return [
			new Route('', HomeController::class),
			new Route('api/hello', HelloController::class),
		];
	}
}
