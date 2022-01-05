<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Csrf;
use PeServer\Core\Uuid;
use PeServer\Core\Route;
use PeServer\Core\Database\Database;
use PeServer\Core\HttpMethod;
use PeServer\Core\HttpStatus;
use PeServer\Core\IMiddleware;
use PeServer\Core\RouteSetting;
use PeServer\Core\MiddlewareResult;
use PeServer\App\Models\AppDatabase;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\MiddlewareArgument;
use PeServer\App\Models\SessionManager;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\SettingController;
use PeServer\App\Controllers\Api\DevelopmentController;
use PeServer\App\Models\Database\Entities\PluginsEntityDao;
use PeServer\App\Models\Domains\Middleware\DevelopmentMiddleware;
use PeServer\App\Models\Domains\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Domains\Middleware\SetupAccountFilterMiddleware;
use PeServer\App\Models\Domains\Middleware\UserPluginEditFilterMiddleware;
use PeServer\App\Models\Domains\Middleware\AdministratorAccountFilterMiddleware;

/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	private const DEFAULT_METHOD = null;

	/**
	 * ルーティング情報設定取得
	 *
	 * @return RouteSetting
	 */
	public static function get(): RouteSetting
	{
		return new RouteSetting(
			[],
			[],
			[
				(new Route('', HomeController::class))
					->addAction('privacy', HttpMethod::get(), 'privacy')
					->addAction('contact', HttpMethod::get(), 'contact_get')
					->addAction('contact', HttpMethod::post(), 'contact_post')
				/* AUTO-FORMAT */,
				(new Route('account', AccountController::class))
					->addAction('login', HttpMethod::get(), 'login_get')
					->addAction('login', HttpMethod::post(), 'login_post')
					->addAction('logout', HttpMethod::get())
					->addAction('user', HttpMethod::get(), self::DEFAULT_METHOD, [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::get(), 'user_edit_get', [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::post(), 'user_edit_post', [UserAccountFilterMiddleware::class, Csrf::middleware()])
					->addAction('user/password', HttpMethod::get(), 'user_password_get', [UserAccountFilterMiddleware::class])
					->addAction('user/password', HttpMethod::post(), 'user_password_post', [UserAccountFilterMiddleware::class, Csrf::middleware()])
					->addAction('user/email', HttpMethod::get(), 'user_email_get', [UserAccountFilterMiddleware::class])
					->addAction('user/email', HttpMethod::post(), 'user_email_post', [UserAccountFilterMiddleware::class, Csrf::middleware()])
					->addAction('user/plugin', HttpMethod::get(), 'user_plugin_register_get', [UserAccountFilterMiddleware::class])
					->addAction('user/plugin', HttpMethod::post(), 'user_plugin_register_post', [UserAccountFilterMiddleware::class, Csrf::middleware()])
					->addAction('user/plugin/:plugin_id@\{?[a-fA-F0-9\-]{32,}\}?', HttpMethod::get(), 'user_plugin_update_get', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class])
					->addAction('user/plugin/:plugin_id@\{?[a-fA-F0-9\-]{32,}\}?', HttpMethod::post(), 'user_plugin_update_post', [UserAccountFilterMiddleware::class, Csrf::middleware(), UserPluginEditFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('setting', SettingController::class, [AdministratorAccountFilterMiddleware::class]))
					->addAction('setup', HttpMethod::get(), 'setup_get', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('setup', HttpMethod::post(), 'setup_post', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('environment', HttpMethod::get())
					->addAction('default-plugin', HttpMethod::get(), 'default_plugin_get')
					->addAction('default-plugin', HttpMethod::post(), 'default_plugin_post')
				/* AUTO-FORMAT */,
				(new Route('api/development', DevelopmentController::class, [DevelopmentMiddleware::class]))
					->addAction('initialize', HttpMethod::post())
					->addAction('administrator', HttpMethod::post())
				/* AUTO-FORMAT */,
			]
		);
	}
}
