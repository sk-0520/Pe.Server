<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;

abstract class StoreConfiguration
{
	/**
	 * クッキー設定を取得。
	 *
	 * @param array{string,string}|null $setting
	 * @return CookieOption
	 */
	public static function getCookie(?array $setting): CookieOption
	{
		$cookie = ArrayUtility::getOr($setting, 'cookie', null);

		$option = CookieOption::create(
			ArrayUtility::getOr($cookie, 'path', '/'),
			ArrayUtility::getOr($cookie, 'span', null),
			ArrayUtility::getOr($cookie, 'secure', false),
			ArrayUtility::getOr($cookie, 'httpOnly', true)
		);

		return $option;
	}

	/**
	 * 一時データ設定を取得。
	 *
	 * @param array{string,string}|null $setting
	 * @param CookieOption $cookie
	 * @return TemporaryOption
	 */
	public static function getTemporary(?array $setting, CookieOption $cookie): TemporaryOption
	{
		$temporary = ArrayUtility::getOr($setting, 'temporary', null);

		$option = TemporaryOption::create(
			ArrayUtility::getOr($temporary, 'name', 'TEMP'),
			ArrayUtility::getOr($temporary, 'save', './temp'),
			$cookie
		);

		return $option;
	}

	/**
	 * セッション設定を取得。
	 *
	 * @param array{string,string}|null $setting
	 * @param CookieOption $cookie
	 * @return SessionOption
	 */
	public static function getSession(?array $setting, CookieOption $cookie): SessionOption
	{
		$session = ArrayUtility::getOr($setting, 'session', null);

		$option = SessionOption::create(
			ArrayUtility::getOr($session, 'name', 'PHPSESSID'),
			ArrayUtility::getOr($session, 'save', ''),
			$cookie
		);

		return $option;
	}

	/**
	 * ストア情報取得。
	 *
	 * @return StoreOption
	 */
	public static function get(): StoreOption
	{
		$setting = ArrayUtility::getOr(AppConfiguration::$json, 'store', null);

		$cookie = self::getCookie($setting);
		$temporary = self::getTemporary($setting, $cookie);
		$session = self::getSession($setting, $cookie);

		return new StoreOption(
			$cookie,
			$temporary,
			$session
		);
	}
}
