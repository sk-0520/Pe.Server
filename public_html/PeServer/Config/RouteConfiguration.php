<?php declare(strict_types=1);
namespace PeServer\Config;

require_once('PeServer/Core/Route.php');
require_once('PeServer/Core/ConfigurationBase.php');

use \PeServer\Core\ConfigurationBase;
use \PeServer\Core\Route;
use \PeServer\App\Controllers\HomeController;
use \PeServer\App\Controllers\Api\HelloController;

final class RouteConfiguration extends ConfigurationBase
{
	function get(): array {
		$result = [
			new Route('', HomeController::class)
			,
			new Route('api/hello', HelloController::class)
			,
		];

		return $result;
	}
}

