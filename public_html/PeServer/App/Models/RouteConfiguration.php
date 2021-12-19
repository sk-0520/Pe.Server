<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\Route;
use \PeServer\Core\HttpMethod;
use \PeServer\App\Controllers\Page\HomeController;
use \PeServer\App\Controllers\Page\AccountController;
use \PeServer\App\Controllers\Api\DevelopmentController;

/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	/**
	 * ルーティング情報設定取得
	 *
	 * @return Route[]
	 */
	public static function get(): array
	{
		return [
			(new Route('', HomeController::class))
				->addAction('privacy', HttpMethod::get(), 'privacy')
				->addAction('contact', HttpMethod::get(), 'contact_get')
				->addAction('contact', HttpMethod::post(), 'contact_post')
			,
			(new Route('account', AccountController::class))
				->addAction('login', HttpMethod::get(), 'login_get')
				->addAction('login', HttpMethod::post(), 'login_post')
			,
			(new Route('api/development', DevelopmentController::class))
				->addAction('initialize', HttpMethod::post())
			,
		];
	}
}
