<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use DateInterval;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Configuration\CookieStoreSetting;
use PeServer\App\Models\Configuration\SessionStoreSetting;
use PeServer\App\Models\Configuration\StoreSetting;
use PeServer\App\Models\Configuration\TemporaryStoreSetting;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Store\CookieOptions;
use PeServer\Core\Store\SessionOptions;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\TemporaryOptions;
use PeServer\Core\Text;
use PeServer\Core\Time;
use PeServer\Core\Store\ISessionHandlerFactory;

/**
 * @phpstan-import-type SameSiteAlias from CookieOptions
 */
abstract class StoreConfiguration
{
	#region function

	/**
	 * Undocumented function
	 *
	 * @param CookieOptions $base
	 * @param CookieStoreSetting $setting
	 * @return CookieOptions
	 */
	private static function mergeCookie(CookieStoreSetting $setting, CookieOptions $base): CookieOptions
	{
		// //get_object_vars($base);
		// $baseSetting = new CookieStoreSetting($base);

		return new CookieOptions(
			Text::ensureIfNotNullOrWhiteSpace($setting->path, $base->path),
			Text::isNullOrWhiteSpace($setting->span) ? $base->span : Time::create($setting->span),
			$setting->secure === null ? $base->secure : $setting->secure,
			$setting->httpOnly === null ? $base->httpOnly : $setting->httpOnly,
			Text::ensureIfNotNullOrWhiteSpace($setting->sameSite, $base->sameSite)
		);
	}

	/**
	 * クッキー設定を取得。
	 *
	 * @param CookieStoreSetting $setting
	 * @return CookieOptions
	 */
	public static function getCookie(CookieStoreSetting $setting): CookieOptions
	{
		/** @var DateInterval|null */
		$span = null;
		if (!Text::isNullOrWhiteSpace($setting->span)) {
			$span = Time::create($setting->span);
		}

		$path = Text::ensureIfNotNullOrWhiteSpace($setting->path, '/');
		$secure = $setting->secure === null ? false : $setting->secure;
		$httpOnly = $setting->httpOnly === null ? true : $setting->httpOnly;
		$sameSite = Text::ensureIfNotNullOrWhiteSpace($setting->sameSite, 'None');

		$options = new CookieOptions(
			$path,
			$span,
			$secure,
			$httpOnly,
			$sameSite
		);

		return $options;
	}

	/**
	 * 一時データ設定を取得。
	 *
	 * @param TemporaryStoreSetting $setting
	 * @param CookieOptions $cookie
	 * @return TemporaryOptions
	 */
	public static function getTemporary(TemporaryStoreSetting $setting, CookieOptions $cookie): TemporaryOptions
	{
		$overwriteCookie = self::mergeCookie($setting->cookie, $cookie);
		if ($overwriteCookie->span === null) {
			$overwriteCookie->span = new DateInterval('PT30M');
		}

		$name = Text::ensureIfNotNullOrWhiteSpace($setting->name, 'TEMP');
		$save = Text::ensureIfNotNullOrWhiteSpace($setting->save, './temp');
		$options = new TemporaryOptions(
			$name,
			$save,
			$overwriteCookie
		);

		return $options;
	}

	/**
	 * セッション設定を取得。
	 *
	 * @param SessionStoreSetting $setting
	 * @param CookieOptions $cookie
	 * @return SessionOptions
	 */
	public static function getSession(SessionStoreSetting $setting, CookieOptions $cookie): SessionOptions
	{
		$overwriteCookie = self::mergeCookie($setting->cookie, $cookie);

		$name = Text::ensureIfNotNullOrWhiteSpace($setting->name, SessionOptions::DEFAULT_NAME);
		$save = Text::ensureIfNotNullOrWhiteSpace($setting->save, SessionOptions::DEFAULT_PATH);

		$options = new SessionOptions(
			$name,
			$save,
			$setting->handlerFactory,
			$overwriteCookie
		);

		return $options;
	}

	/**
	 * ストア情報取得。
	 *
	 * @param StoreSetting $setting;
	 * @return StoreOptions
	 */
	public static function build(StoreSetting $setting): StoreOptions
	{
		$cookie = self::getCookie($setting->cookie);
		$temporary = self::getTemporary($setting->temporary, $cookie);
		$session = self::getSession($setting->session, $cookie);

		return new StoreOptions(
			$cookie,
			$temporary,
			$session
		);
	}

	#endregion
}
