<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\ActionOption;
use \PeServer\Core\Route;
use \PeServer\Core\FilterArgument;
use \PeServer\Core\HttpMethod;
use \PeServer\App\Controllers\Page\HomeController;
use \PeServer\App\Controllers\Page\SettingController;
use \PeServer\App\Controllers\Page\AccountController;
use \PeServer\App\Controllers\Page\ErrorController;
use \PeServer\App\Controllers\Api\DevelopmentController;
use \PeServer\Core\HttpStatus;
use \PeServer\Core\Store\SessionStore;

/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	private const DEFAULT_METHOD = null;

	/**
	 * Undocumented function
	 *
	 * @param FilterArgument $argument
	 * @param string[] $levels
	 * @return HttpStatus
	 */
	private static function filterPageAccount(FilterArgument $argument, array $levels): HttpStatus
	{
		if (!$argument->session->tryGet(SessionManager::ACCOUNT, $account)) {
			return HttpStatus::forbidden();
		}

		foreach ($levels as $level) {
			if ($account['level'] === $level) {
				return HttpStatus::doExecute();
			}
		}

		return HttpStatus::forbidden();
	}

	private static ?ActionOption $user = null;
	private static function user(): ActionOption
	{
		if (!is_null(self::$user)) {
			return self::$user;
		}

		$options = new ActionOption();
		$options->errorControllerName = ErrorController::class;
		$options->filter = function (FilterArgument $argument) {
			return self::filterPageAccount($argument, [UserLevel::USER, UserLevel::ADMINISTRATOR]);
		};

		return self::$user = $options;
	}

	private static ?ActionOption $setup = null;
	private static function setup(): ActionOption
	{
		if (!is_null(self::$setup)) {
			return self::$setup;
		}

		$options = new ActionOption();
		$options->errorControllerName = ErrorController::class;
		$options->filter = function (FilterArgument $argument) {
			return self::filterPageAccount($argument, [UserLevel::SETUP]);
		};

		return self::$setup = $options;
	}

	private static ?ActionOption $admin = null;
	private static function admin(): ActionOption
	{
		if (!is_null(self::$admin)) {
			return self::$admin;
		}

		$options = new ActionOption();
		$options->errorControllerName = ErrorController::class;
		$options->filter = function (FilterArgument $argument) {
			return self::filterPageAccount($argument, [UserLevel::ADMINISTRATOR]);
		};

		return self::$admin = $options;
	}

	private static ?ActionOption $development = null;
	private static function development(): ActionOption
	{
		if (!is_null(self::$development)) {
			return self::$development;
		}

		$options = new ActionOption();
		$options->errorControllerName = ErrorController::class;
		$options->filter = function (FilterArgument $argument) {
			if (AppConfiguration::isProductionEnvironment()) {
				$argument->logger->warn('本番環境での実行は抑制');
				return HttpStatus::forbidden();
			}

			return HttpStatus::doExecute();
		};

		return self::$development = $options;
	}


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
			/* AUTO-FORMAT */,
			(new Route('account', AccountController::class))
				->addAction('login', HttpMethod::get(), 'login_get')
				->addAction('login', HttpMethod::post(), 'login_post')
				->addAction('logout', HttpMethod::get())
				->addAction('user', HttpMethod::get(), self::DEFAULT_METHOD, self::user())
				->addAction('user/edit', HttpMethod::get(), 'user_edit_get', self::user())
				->addAction('user/edit', HttpMethod::post(), 'user_edit_post', self::user())
				->addAction('user/password', HttpMethod::get(), 'user_password_get', self::user())
				->addAction('user/password', HttpMethod::post(), 'user_password_post', self::user())
			/* AUTO-FORMAT */,
			(new Route('setting', SettingController::class, self::admin()))
				->addAction('setup', HttpMethod::get(), 'setup_get', self::setup())
				->addAction('setup', HttpMethod::post(), 'setup_post', self::setup())
			/* AUTO-FORMAT */,
			(new Route('api/development', DevelopmentController::class, self::development()))
				->addAction('initialize', HttpMethod::post())
				->addAction('administrator', HttpMethod::post())
			/* AUTO-FORMAT */,
		];
	}
}
