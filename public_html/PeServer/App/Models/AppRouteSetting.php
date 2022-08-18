<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\App\Controllers\Api\DevelopmentApiController;
use PeServer\App\Controllers\Api\PluginApiController;
use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\AjaxController;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\PluginController;
use PeServer\App\Controllers\Page\SettingController;
use PeServer\App\Models\Middleware\AdministratorAccountFilterMiddleware;
use PeServer\App\Models\Middleware\DevelopmentMiddleware;
use PeServer\App\Models\Middleware\PluginIdMiddleware;
use PeServer\App\Models\Middleware\SetupAccountFilterMiddleware;
use PeServer\App\Models\Middleware\SignupStep1FilterMiddleware;
use PeServer\App\Models\Middleware\SignupStep2FilterMiddleware;
use PeServer\App\Models\Middleware\UserAccountFilterMiddleware;
use PeServer\App\Models\Middleware\UserPluginEditFilterMiddleware;
use PeServer\Core\DefaultValue;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\Middleware\CsrfMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceMiddleware;
use PeServer\Core\Mvc\Middleware\PerformanceShutdownMiddleware;
use PeServer\Core\Mvc\Route;
use PeServer\Core\Mvc\RouteSetting;

/**
 * アプリルーティング設定。
 */
final class AppRouteSetting extends RouteSetting
{
	private const SIGNUP_TOKEN = '[a-zA-Z0-9]{80}';
	private const PLUGIN_ID = '\{?[a-fA-F0-9\-]{32,}\}?';

	public function __construct()
	{
		$isProduction = Environment::isProduction();
		parent::__construct(
			[],
			[
				...($isProduction ? [PerformanceMiddleware::class] : [])
			],
			[
				...($isProduction ? [PerformanceShutdownMiddleware::class] : [])
			],
			[],
			[
				(new Route(DefaultValue::EMPTY_STRING, HomeController::class))
					->addAction('about', HttpMethod::gets(), 'about')
					->addAction('about/privacy', HttpMethod::gets(), 'privacy')
					->addAction('about/contact', HttpMethod::gets(), 'contact_get')
					->addAction('about/contact', HttpMethod::post(), 'contact_post')
					->addAction('api-doc', HttpMethod::gets(), 'api')
					->addAction('dev/exception', HttpMethod::gets(), 'exception', [DevelopmentMiddleware::class])
					->addAction(':path@[a-zA-z0-9_\(\)\-]+\.[a-zA-z0-9_\(\)\-]+', HttpMethod::gets(), 'wildcard')
				/* AUTO-FORMAT */,
				(new Route('account', AccountController::class))
					->addAction('login', HttpMethod::gets(), 'login_get')
					->addAction('login', HttpMethod::post(), 'login_post')
					->addAction('logout', HttpMethod::gets())
					->addAction('signup', HttpMethod::gets(), 'signup_step1_get', [SignupStep1FilterMiddleware::class])
					->addAction('signup', HttpMethod::post(), 'signup_step1_post', [SignupStep1FilterMiddleware::class])
					->addAction('signup/notify', HttpMethod::gets(), 'signup_notify', [SignupStep1FilterMiddleware::class])
					->addAction('signup/:token@' . self::SIGNUP_TOKEN, HttpMethod::gets(), 'signup_step2_get', [SignupStep1FilterMiddleware::class, SignupStep2FilterMiddleware::class])
					->addAction('signup/:token@' . self::SIGNUP_TOKEN, HttpMethod::post(), 'signup_step2_post', [SignupStep1FilterMiddleware::class, SignupStep2FilterMiddleware::class])
					->addAction('user', HttpMethod::gets(), Route::DEFAULT_METHOD, [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::gets(), 'user_edit_get', [UserAccountFilterMiddleware::class])
					->addAction('user/edit', HttpMethod::post(), 'user_edit_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/password', HttpMethod::gets(), 'user_password_get', [UserAccountFilterMiddleware::class])
					->addAction('user/password', HttpMethod::post(), 'user_password_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/email', HttpMethod::gets(), 'user_email_get', [UserAccountFilterMiddleware::class])
					->addAction('user/email', HttpMethod::post(), 'user_email_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin', HttpMethod::gets(), 'user_plugin_register_get', [UserAccountFilterMiddleware::class])
					->addAction('user/plugin', HttpMethod::post(), 'user_plugin_register_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::gets(), 'user_plugin_update_get', [UserAccountFilterMiddleware::class, UserPluginEditFilterMiddleware::class])
					->addAction('user/plugin/:plugin_id@' . self::PLUGIN_ID, HttpMethod::post(), 'user_plugin_update_post', [UserAccountFilterMiddleware::class, CsrfMiddleware::class, UserPluginEditFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('plugin', PluginController::class))
					->addAction(':plugin_id@' . self::PLUGIN_ID, HttpMethod::gets(), 'detail', [PluginIdMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('setting', SettingController::class, [AdministratorAccountFilterMiddleware::class]))
					->addAction('setup', HttpMethod::get(), 'setup_get', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('setup', HttpMethod::post(), 'setup_post', [Route::CLEAR_MIDDLEWARE, SetupAccountFilterMiddleware::class])
					->addAction('environment', HttpMethod::get())
					->addAction('configuration', HttpMethod::get())
					->addAction('database-maintenance', HttpMethod::get(), 'database_maintenance_get')
					->addAction('database-maintenance', HttpMethod::post(), 'database_maintenance_post')
					->addAction('php-evaluate', HttpMethod::get(), 'php_evaluate_get')
					->addAction('php-evaluate', HttpMethod::post(), 'php_evaluate_post')
					->addAction('default-plugin', HttpMethod::get(), 'default_plugin_get')
					->addAction('default-plugin', HttpMethod::post(), 'default_plugin_post')
					->addAction('cache-rebuild', HttpMethod::get(), 'cache_rebuild')
					->addAction('plugin-category', HttpMethod::get(), 'plugin_category_get')
					->addAction('log', HttpMethod::get(), 'log_list')
					->addAction('log/:log_name@\w+\.log', HttpMethod::get(), 'log_detail')
					->addAction('markdown', HttpMethod::get(), 'markdown')
				/* AUTO-FORMAT */,
				(new Route('ajax', AjaxController::class, [UserAccountFilterMiddleware::class]))
					->addAction('markdown', HttpMethod::post(), 'markdown')
					->addAction('plugin-category', HttpMethod::post(), 'plugin_category_post', [AdministratorAccountFilterMiddleware::class])
					->addAction('plugin-category/:plugin_category_id@.+', HttpMethod::patch(), 'plugin_category_patch', [AdministratorAccountFilterMiddleware::class])
					->addAction('plugin-category/:plugin_category_id@.+', HttpMethod::delete(), 'plugin_category_delete', [AdministratorAccountFilterMiddleware::class])
				/* AUTO-FORMAT */,
				(new Route('api/development', DevelopmentApiController::class, [DevelopmentMiddleware::class]))
					->addAction('initialize', HttpMethod::post())
					->addAction('administrator', HttpMethod::post())
				/* AUTO-FORMAT */,
				(new Route('api/plugin', PluginApiController::class))
					->addAction('exists', HttpMethod::post(), 'exists')
					->addAction('generate-plugin-id', HttpMethod::gets(), 'generate_plugin_id')
					->addAction('information', HttpMethod::post(), 'information')
				/* AUTO-FORMAT */,
			]
		);
	}
}
