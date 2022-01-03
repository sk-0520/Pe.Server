<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Csrf;
use PeServer\Core\Route;
use PeServer\Core\HttpMethod;
use PeServer\Core\HttpStatus;
use PeServer\Core\Environment;
use PeServer\Core\FilterResult;
use PeServer\Core\IActionFilter;
use PeServer\Core\FilterArgument;
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
use PeServer\Core\Database;
use PeServer\Core\Uuid;

/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	private const DEFAULT_METHOD = null;

	/**
	 * ルーティング情報設定取得
	 *
	 * @return array{global_filters:IActionFilter[],action_filters:IActionFilter[],routes:Route[]}
	 */
	public static function get(): array
	{
		return [
			'global_filters' => [],
			'action_filters' => [],
			'routes' => [
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
					->addAction('user/edit', HttpMethod::post(), 'user_edit_post', [self::user(), Csrf::csrf()])
					->addAction('user/password', HttpMethod::get(), 'user_password_get', [self::user()])
					->addAction('user/password', HttpMethod::post(), 'user_password_post', [self::user(), Csrf::csrf()])
					->addAction('user/email', HttpMethod::get(), 'user_email_get', [self::user()])
					->addAction('user/email', HttpMethod::post(), 'user_email_post', [self::user(), Csrf::csrf()])
					->addAction('user/plugin', HttpMethod::get(), 'user_plugin_register_get', [self::user()])
					->addAction('user/plugin', HttpMethod::post(), 'user_plugin_register_post', [self::user(), Csrf::csrf()])
					->addAction('user/plugin/:plugin_id@\{?[a-fA-F0-9\-]{32,}\}?', HttpMethod::get(), 'user_plugin_update_get', [self::user(), self::plugin()])
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
		];
	}

	protected function openDatabase(): Database
	{
		return AppDatabase::open();
	}


	/**
	 * Undocumented function
	 *
	 * @param FilterArgument $argument
	 * @param string[] $levels
	 * @return FilterResult
	 */
	protected static function filterPageAccount(FilterArgument $argument, array $levels): FilterResult
	{
		if (!$argument->session->tryGet(SessionManager::ACCOUNT, $account)) {
			return FilterResult::error(HttpStatus::forbidden());
		}

		foreach ($levels as $level) {
			if ($account['level'] === $level) {
				return FilterResult::none();
			}
		}

		return FilterResult::error(HttpStatus::forbidden());
	}

	private static ?IActionFilter $user = null;
	private static function user(): IActionFilter
	{
		return self::$user ??= new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::USER, UserLevel::ADMINISTRATOR]);
			}
		};
	}

	private static ?IActionFilter $setup = null;
	private static function setup(): IActionFilter
	{
		return self::$setup ??= new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::SETUP]);
			}
		};
	}

	private static ?IActionFilter $admin = null;
	private static function admin(): IActionFilter
	{
		return self::$admin ??= new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::ADMINISTRATOR]);
			}
		};
	}

	private static ?IActionFilter $development = null;
	private static function development(): IActionFilter
	{
		return self::$development ??= new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				if (Environment::isProduction()) {
					$argument->logger->warn('本番環境での実行は抑制');
					return FilterResult::error(HttpStatus::forbidden());
				}

				return FilterResult::none();
			}
		};
	}

	private static ?IActionFilter $plugin = null;
	private static function plugin(): IActionFilter
	{
		return self::$plugin ??= new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				$pluginIdState = $argument->request->exists('plugin_id');
				if($pluginIdState['exists'] && $pluginIdState['type'] === ActionRequest::REQUEST_URL) {
					$pluginId = $argument->request->getValue('plugin_id');
					// ここにきてるってことはユーザーフィルタを通過しているのでセッションを見る必要はないけど一応ね
					if(Uuid::isGuid($pluginId) && SessionManager::hasAccount()) {
						$pluginId = Uuid::adjustGuid($pluginId);
						$account = SessionManager::getAccount();
						$database = $this->openDatabase();
						$pluginsEntityDao = new PluginsEntityDao($database);
						if($pluginsEntityDao->selectIsUserPlugin($pluginId, $account['user_id'])) {
							return FilterResult::none();
						}
					}
				}

				return FilterResult::error(HttpStatus::notFound());
			}
		};
	}
}
