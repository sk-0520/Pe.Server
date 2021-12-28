<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\ArrayUtility;
use \PeServer\Core\Store\CookieOption;
use \PeServer\Core\Store\SessionOption;

abstract class StoreConfiguration
{
	/**
	 * Undocumented function
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
	 * Undocumented function
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
			$cookie
		);

		return $option;
	}

	/**
	 * ストア情報取得。
	 *
	 * @return array{cookie:CookieOption,session:SessionOption}
	 */
	public static function get(): array
	{
		$setting = ArrayUtility::getOr(AppConfiguration::$json, 'store', null);

		$cookie = self::getCookie($setting);
		$session = self::getSession($setting, $cookie);

		return [
			'cookie' => $cookie,
			'session' => $session,
		];
	}
}
