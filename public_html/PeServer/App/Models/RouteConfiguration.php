<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Route;
use PeServer\Core\RouteSetting;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\App\Controllers\Page\AjaxController;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\PluginController;
use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\SettingController;
use PeServer\App\Controllers\Api\PluginApiController;
use PeServer\App\Models\Middleware\PluginIdMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceMiddleware;
use PeServer\App\Models\Middleware\DevelopmentMiddleware;
use PeServer\App\Controllers\Api\DevelopmentApiController;
use PeServer\App\Models\Middleware\SignupStep1FilterMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceShutdownMiddleware;
use PeServer\App\Models\Middleware\SetupAccountFilterMiddleware;
use PeServer\App\Models\Middleware\UserPluginEditFilterMiddleware;
use PeServer\App\Models\Middleware\AdministratorAccountFilterMiddleware;


/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	private const DEFAULT_METHOD = null;

	private const PLUGIN_ID = '\{?[a-fA-F0-9\-]{32,}\}?';

	/**
	 * ルーティング情報設定取得
	 *
	 * @return RouteSetting
	 */
	public static function get(): RouteSetting
	{
		return new RouteSetting(
			[],
			[
				PerformanceMiddleware::class
			],
			[
				PerformanceShutdownMiddleware::class
			],
			[],
			[
				(new Route('', HomeController::class))
					->addAction('about', HttpMethod::get(), 'about')
					->addAction('about/privacy', HttpMethod::get(), 'privacy')
					->addAction('about/contact', HttpMethod::get(), 'contact_get')
					->addAction('about/contact', HttpMethod::post(), 'contact_post')
					->addAction('api-doc', HttpMethod::get(), 'api')
					/* AUTO-FORMAT */,
				(new Route('account', AccountController::class))
					->addAction('login', HttpMethod::get(), 'login_get')
					->addAction('login', HttpMethod::post(), 'login_post')
					->addAction('logout', HttpMethod::get())
					->addAction('signup', HttpMethod::get(), 'signup_step1_get', [SignupStep1FilterMiddleware::class])
					->addAction('signup', HttpMethod::post(), 'signup_step1_post', [SignupStep1FilterMiddleware::class])
					->addAction('signup/:token@[a-zA-Z0-9]{80}', HttpMethod::get(), 'signup_step2_get', [SignupStep1FilterMiddleware::class, SignupStep2FilterMiddleware::class])
					->addAction('signup/:token@[a-zA-Z0-9]{80}', HttpMethod::post(), 'signup_step2_post', [SignupStep1FilterMiddleware::class, SignupStep2FilterMiddleware::class])
					->addAction('user', HttpMethod::get(), self::DEFAULT_METHOD, [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::get(), 'user_edit_get', [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::post(), 'user_edit_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/password', HttpMethod::get(), 'user_password_get', [UserAccountFilterMiddleware::class])
					->addAction('user/password', HttpMethod::post(), 'user_password_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/email', HttpMethod::get(), 'user_email_get', [UserAccountFilterMiddleware::class])
					->addAction('user/email', HttpMethod::post(), 'user_email_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin', HttpMethod::get(), 'user_plugin_register_get', [UserAccountFilterMiddleware::class])
					->addAction('user/plugin', HttpMethod::post(), 'user_plugin_register_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::get(), 'user_plugin_update_get', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::post(), 'user_plugin_update_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class, UserPluginEditFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('plugin', PluginController::class))
					->addAction(':plugin_id@' . self::PLUGIN_ID, HttpMethod::get(), 'detail', [PluginIdMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('setting', SettingController::class, [AdministratorAccountFilterMiddleware::class]))
					->addAction('setup', HttpMethod::get(), 'setup_get', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('setup', HttpMethod::post(), 'setup_post', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('environment', HttpMethod::get())
					->addAction('default-plugin', HttpMethod::get(), 'default_plugin_get')
					->addAction('default-plugin', HttpMethod::post(), 'default_plugin_post')
					->addAction('log', HttpMethod::get(), 'log_list')
					->addAction('log/:log_name@\w+\.log', HttpMethod::get(), 'log_detail')
					->addAction('markdown', HttpMethod::get(), 'markdown')
				/* AUTO-FORMAT */,
				(new Route('ajax', AjaxController::class, [UserAccountFilterMiddleware::class]))
					->addAction('markdown', HttpMethod::post(), 'markdown')
				/* AUTO-FORMAT */,
				(new Route('api/development', DevelopmentApiController::class, [DevelopmentMiddleware::class]))
					->addAction('initialize', HttpMethod::post())
					->addAction('administrator', HttpMethod::post())
				/* AUTO-FORMAT */,
				(new Route('api/plugin', PluginApiController::class))
					->addAction('exists', HttpMethod::post(), 'exists')
				/* AUTO-FORMAT */,
			]
		);
	}
}
