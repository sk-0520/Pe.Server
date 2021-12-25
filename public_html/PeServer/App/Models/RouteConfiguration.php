<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\ActionOptions;
use \PeServer\Core\Route;
use \PeServer\Core\HttpMethod;
use \PeServer\App\Controllers\Page\HomeController;
use \PeServer\App\Controllers\Page\SettingController;
use \PeServer\App\Controllers\Page\AccountController;
use \PeServer\App\Controllers\Page\ErrorController;
use \PeServer\App\Controllers\Api\DevelopmentController;
use PeServer\Core\HttpStatusCode;
use PeServer\Core\Mvc\SessionStore;

/**
 * ルーティング情報設定。
 */
abstract class RouteConfiguration
{
	/**
	 * Undocumented function
	 *
	 * @param SessionStore $session
	 * @param string[] $levels
	 * @return boolean
	 */
	private static function filterPageAccount(SessionStore $session, array $levels): int
	{
		if (!$session->tryGet(SessionKey::ACCOUNT, $account)) {
			return HttpStatusCode::FORBIDDEN;
		}

		foreach ($levels as $level) {
			if ($account['level'] === $level) {
				return HttpStatusCode::DO_EXECUTE;
			}
		}

		return HttpStatusCode::FORBIDDEN;
	}

	private static ?ActionOptions $_user = null;
	private static function user(): ActionOptions // @phpstan-ignore-line
	{
		if (!is_null(self::$_user)) {
			return self::$_user;
		}

		$options = new ActionOptions();
		$options->errorControllerName = ErrorController::class;
		$options->sessionFilter = function (SessionStore $s) {
			return self::filterPageAccount($s, [UserLevel::USER]);
		};

		return self::$_user = $options;
	}

	private static ?ActionOptions $_setup = null;
	private static function setup(): ActionOptions
	{
		if (!is_null(self::$_setup)) {
			return self::$_setup;
		}

		$options = new ActionOptions();
		$options->errorControllerName = ErrorController::class;
		$options->sessionFilter = function (SessionStore $s) {
			return self::filterPageAccount($s, [UserLevel::SETUP]);
		};

		return self::$_setup = $options;
	}

	private static ?ActionOptions $_admin = null;
	private static function admin(): ActionOptions
	{
		if (!is_null(self::$_admin)) {
			return self::$_admin;
		}

		$options = new ActionOptions();
		$options->errorControllerName = ErrorController::class;
		$options->sessionFilter = function (SessionStore $s) {
			return self::filterPageAccount($s, [UserLevel::ADMINISTRATOR]);
		};

		return self::$_admin = $options;
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
			/* AUTO-FORMAT */,
			(new Route('setting', SettingController::class, self::admin()))
				->addAction('setup', HttpMethod::get(), 'setup_get', self::setup())
				->addAction('setup', HttpMethod::post(), 'setup_post', self::setup())
			/* AUTO-FORMAT */,
			(new Route('api/development', DevelopmentController::class))
				->addAction('initialize', HttpMethod::post())
			/* AUTO-FORMAT */,
		];
	}
}
