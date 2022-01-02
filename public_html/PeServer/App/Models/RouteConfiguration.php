<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\Csrf;
use \PeServer\Core\Route;
use \PeServer\Core\HttpMethod;
use \PeServer\Core\HttpStatus;
use \PeServer\Core\FilterResult;
use \PeServer\Core\ActionOption;
use \PeServer\Core\IActionFilter;
use \PeServer\Core\FilterArgument;
use \PeServer\Core\Mvc\ActionResult;
use \PeServer\Core\Store\SessionStore;
use \PeServer\App\Controllers\Page\HomeController;
use \PeServer\App\Controllers\Page\ErrorController;
use \PeServer\App\Controllers\Page\AccountController;
use \PeServer\App\Controllers\Page\SettingController;
use \PeServer\App\Controllers\Api\DevelopmentController;

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

	private static ?ActionOption $user = null;
	private static function user(): ActionOption
	{
		if (!is_null(self::$user)) {
			return self::$user;
		}

		$option = new ActionOption();
		$option->errorControllerName = ErrorController::class;
		$option->filter = new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::USER, UserLevel::ADMINISTRATOR]);
			}
		};

		return self::$user = $option;
	}

	private static ?ActionOption $setup = null;
	private static function setup(): ActionOption
	{
		if (!is_null(self::$setup)) {
			return self::$setup;
		}

		$option = new ActionOption();
		$option->errorControllerName = ErrorController::class;
		$option->filter = new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::SETUP]);
			}
		};

		return self::$setup = $option;
	}

	private static ?ActionOption $admin = null;
	private static function admin(): ActionOption
	{
		if (!is_null(self::$admin)) {
			return self::$admin;
		}

		$option = new ActionOption();
		$option->errorControllerName = ErrorController::class;
		$option->filter  = new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				return self::filterPageAccount($argument, [UserLevel::ADMINISTRATOR]);
			}
		};

		return self::$admin = $option;
	}

	private static ?ActionOption $development = null;
	private static function development(): ActionOption
	{
		if (!is_null(self::$development)) {
			return self::$development;
		}

		$option = new ActionOption();
		$option->errorControllerName = ErrorController::class;
		$option->filter = new class extends RouteConfiguration implements IActionFilter
		{
			public function filtering(FilterArgument $argument): FilterResult
			{
				if (AppConfiguration::isProductionEnvironment()) {
					$argument->logger->warn('本番環境での実行は抑制');
					return FilterResult::error(HttpStatus::forbidden());
				}

				return FilterResult::none();
			}
		};

		return self::$development = $option;
	}
}
