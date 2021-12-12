<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\Route;
use \PeServer\App\Controllers\HomeController;
use \PeServer\App\Controllers\Api\DevelopmentController;
use PeServer\Core\HttpMethod;

/**
 * ルーティング情報設定。
 */
class RouteConfiguration
{
	/**
	 * ルーティング情報設定取得
	 *
	 * @return Route[]
	 */
	public static function get(): array
	{
		return [
			(new Route('', HomeController::class)),
			(new Route('api/development', DevelopmentController::class))
				->addAction(HttpMethod::POST, 'initialize')
			,
		];
	}
}
