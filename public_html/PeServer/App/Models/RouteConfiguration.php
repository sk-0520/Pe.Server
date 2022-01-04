<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Csrf;
use PeServer\Core\Uuid;
use PeServer\Core\Route;
use PeServer\Core\Database;
use PeServer\Core\HttpMethod;
use PeServer\Core\HttpStatus;
use PeServer\Core\Environment;
use PeServer\Core\MiddlewareResult;
use PeServer\Core\RouteSetting;
use PeServer\Core\IMiddleware;
use PeServer\Core\MiddlewareArgument;
use PeServer\Core\Mvc\ActionResult;
use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Store\SessionStore;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Controllers\Page\ErrorController;
use PeServer\App\Controllers\Page\AccountController;
use PeServer\App\Controllers\Page\SettingController;
use PeServer\App\Controllers\Api\DevelopmentController;
use PeServer\App\Models\Database\Entities\PluginsEntityDao;

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
					->addAction('user', HttpMethod::get(), self::DEFAULT_METHOD, [self::user()])
					->addAction('user/edit', HttpMethod::get(), 'user_edit_get', [self::user()])
					->addAction('user/edit', HttpMethod::post(), 'user_edit_post', [self::user(), Csrf::middleware()])
					->addAction('user/password', HttpMethod::get(), 'user_password_get', [self::user()])
					->addAction('user/password', HttpMethod::post(), 'user_password_post', [self::user(), Csrf::middleware()])
					->addAction('user/email', HttpMethod::get(), 'user_email_get', [self::user()])
					->addAction('user/email', HttpMethod::post(), 'user_email_post', [self::user(), Csrf::middleware()])
					->addAction('user/plugin', HttpMethod::get(), 'user_plugin_register_get', [self::user()])
					->addAction('user/plugin', HttpMethod::post(), 'user_plugin_register_post', [self::user(), Csrf::middleware()])
					->addAction('user/plugin/:plugin_id@\{?[a-fA-F0-9\-]{32,}\}?', HttpMethod::get(), 'user_plugin_update_get', [self::user(), self::plugin()])
					->addAction('user/plugin/:plugin_id@\{?[a-fA-F0-9\-]{32,}\}?', HttpMethod::post(), 'user_plugin_update_post', [self::user(), Csrf::middleware(), self::plugin()])
				/* AUTO-FORMAT */,
				(new Route('setting', SettingController::class, [self::admin()]))
					->addAction('setup', HttpMethod::get(), 'setup_get', [self::setup()])
					->addAction('setup', HttpMethod::post(), 'setup_post', [self::setup()])
				/* AUTO-FORMAT */,
				(new Route('api/development', DevelopmentController::class, [self::development()]))
					->addAction('initialize', HttpMethod::post())
					->addAction('administrator', HttpMethod::post())
				/* AUTO-FORMAT */,
			]
		);
	}

	protected function openDatabase(): Database
	{
		return AppDatabase::open();
	}


	/**
	 * アカウント用フィルタ処理。
	 *
	 * @param MiddlewareArgument $argument
	 * @param string[] $levels
	 * @return MiddlewareResult
	 */
	protected static function filterPageAccount(MiddlewareArgument $argument, array $levels): MiddlewareResult
	{
		if (!$argument->session->tryGet(SessionManager::ACCOUNT, $account)) {
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		foreach ($levels as $level) {
			if ($account['level'] === $level) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::forbidden());
	}

	private static ?IMiddleware $user = null;
	private static function user(): IMiddleware
	{
		return self::$user ??= new class extends RouteConfiguration implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				return self::filterPageAccount($argument, [UserLevel::USER, UserLevel::ADMINISTRATOR]);
			}
		};
	}

	private static ?IMiddleware $setup = null;
	private static function setup(): IMiddleware
	{
		return self::$setup ??= new class extends RouteConfiguration implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				return self::filterPageAccount($argument, [UserLevel::SETUP]);
			}
		};
	}

	private static ?IMiddleware $admin = null;
	private static function admin(): IMiddleware
	{
		return self::$admin ??= new class extends RouteConfiguration implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				return self::filterPageAccount($argument, [UserLevel::ADMINISTRATOR]);
			}
		};
	}

	private static ?IMiddleware $development = null;
	private static function development(): IMiddleware
	{
		return self::$development ??= new class extends RouteConfiguration implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				if (Environment::isProduction()) {
					$argument->logger->warn('本番環境での実行は抑制');
					return MiddlewareResult::error(HttpStatus::forbidden());
				}

				return MiddlewareResult::none();
			}
		};
	}

	private static ?IMiddleware $plugin = null;
	private static function plugin(): IMiddleware
	{
		return self::$plugin ??= new class extends RouteConfiguration implements IMiddleware
		{
			public function handle(MiddlewareArgument $argument): MiddlewareResult
			{
				$pluginIdState = $argument->request->exists('plugin_id');
				if ($pluginIdState['exists'] && $pluginIdState['type'] === ActionRequest::REQUEST_URL) {
					$pluginId = $argument->request->getValue('plugin_id');
					// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
					if (Uuid::isGuid($pluginId) && SessionManager::hasAccount()) {
						$pluginId = Uuid::adjustGuid($pluginId);
						$account = SessionManager::getAccount();
						$database = $this->openDatabase();
						$pluginsEntityDao = new PluginsEntityDao($database);
						if ($pluginsEntityDao->selectIsUserPlugin($pluginId, $account['user_id'])) {
							return MiddlewareResult::none();
						}
					}
				}

				return MiddlewareResult::error(HttpStatus::notFound());
			}
		};
	}
}
