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
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\TemporaryOption;
use PeServer\Core\Text;
use PeServer\Core\Time;

abstract class StoreConfiguration
{
	#region function

	/**
	 * Undocumented function
	 *
	 * @param CookieOption $base
	 * @param CookieStoreSetting $setting
	 * @return CookieOption
	 */
	private static function mergeCookie(CookieStoreSetting $setting, CookieOption $base): CookieOption
	{
		// //get_object_vars($base);
		// $baseSetting = new CookieStoreSetting($base);

		return new CookieOption(
			Text::requireNotNullOrWhiteSpace($setting->path, $base->path),
			Text::isNullOrWhiteSpace($setting->span) ? $base->span : Time::create($setting->span),
			$setting->secure === null ? $base->secure : $setting->secure,
			$setting->httpOnly === null ? $base->httpOnly : $setting->httpOnly,
			Text::requireNotNullOrWhiteSpace($setting->sameSite, $base->sameSite) //@phpstan-ignore-line not null
		);
	}

	/**
	 * クッキー設定を取得。
	 *
	 * @param CookieStoreSetting $setting
	 * @return CookieOption
	 */
	public static function getCookie(CookieStoreSetting $setting): CookieOption
	{
		/** @var DateInterval|null */
		$span = null;
		if (!Text::isNullOrWhiteSpace($setting->span)) {
			$span = Time::create($setting->span);
		}

		$path = Text::requireNotNullOrWhiteSpace($setting->path, '/');
		$secure = $setting->secure === null ? false : $setting->secure;
		$httpOnly = $setting->httpOnly === null ? true : $setting->httpOnly;
		/** @phpstan-var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict' */
		$sameSite = Text::requireNotNullOrWhiteSpace($setting->sameSite, 'None');

		$option = new CookieOption(
			$path,
			$span,
			$secure,
			$httpOnly,
			(string)$sameSite
		);

		return $option;
	}

	/**
	 * 一時データ設定を取得。
	 *
	 * @param TemporaryStoreSetting $setting
	 * @param CookieOption $cookie
	 * @return TemporaryOption
	 */
	public static function getTemporary(TemporaryStoreSetting $setting, CookieOption $cookie): TemporaryOption
	{
		$overwriteCookie = self::mergeCookie($setting->cookie, $cookie);
		if ($overwriteCookie->span === null) {
			$overwriteCookie->span = new DateInterval('PT30M');
		}

		$name = Text::requireNotNullOrWhiteSpace($setting->name, 'TEMP');
		$save = Text::requireNotNullOrWhiteSpace($setting->save, './temp');
		$option = new TemporaryOption(
			$name,
			$save,
			$overwriteCookie
		);

		return $option;
	}

	/**
	 * セッション設定を取得。
	 *
	 * @param SessionStoreSetting $setting
	 * @param CookieOption $cookie
	 * @return SessionOption
	 */
	public static function getSession(SessionStoreSetting $setting, CookieOption $cookie): SessionOption
	{
		$overwriteCookie = self::mergeCookie($setting->cookie, $cookie);

		/** @phpstan-var non-empty-string $name */
		$name = Text::requireNotNullOrWhiteSpace($setting->name, SessionOption::DEFAULT_NAME);
		$save = Text::requireNotNullOrWhiteSpace($setting->save, Text::EMPTY);

		$option = new SessionOption(
			$name,
			$save,
			$overwriteCookie
		);

		return $option;
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
