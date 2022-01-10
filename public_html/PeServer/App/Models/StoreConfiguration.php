<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use DateInterval;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Store\StoreOption;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;

abstract class StoreConfiguration
{
	/**
	 * Undocumented function
	 *
	 * @param CookieOption $base
	 * @param array<string,mixed>|null $setting
	 * @return CookieOption
	 */
	private static function mergeCookie(CookieOption $base, ?array $setting): CookieOption
	{
		//get_object_vars($base);
		$baseSetting = [
			'path' => $base->path,
			'span' => $base->span,
			'secure' => $base->secure,
			'httpOnly' => $base->httpOnly,
			'sameSite' => $base->sameSite,
		];
		$overwriteSetting = [
			'cookie' => array_replace_recursive($baseSetting, ArrayUtility::getOr($setting, 'cookie', []))
		];

		$overwriteCookie = self::getCookie($overwriteSetting);

		return $overwriteCookie;
	}

	/**
	 * クッキー設定を取得。
	 *
	 * @param array<string,array<string,mixed>>|null $setting
	 * @return CookieOption
	 */
	public static function getCookie(?array $setting): CookieOption
	{
		$cookie = ArrayUtility::getOr($setting, 'cookie', null);

		$spanSetting = ArrayUtility::getOr($cookie, 'span', null);
		/** @var DateInterval|null */
		$span = null;
		if (!is_null($spanSetting)) {
			$span = new DateInterval($spanSetting);
		}

		$option = CookieOption::create(
			ArrayUtility::getOr($cookie, 'path', '/'),
			$span,
			ArrayUtility::getOr($cookie, 'secure', false),
			ArrayUtility::getOr($cookie, 'httpOnly', true),
			ArrayUtility::getOr($cookie, 'sameSite', 'None'),
		);

		return $option;
	}

	/**
	 * 一時データ設定を取得。
	 *
	 * @param array<string,string>|null $setting
	 * @param CookieOption $cookie
	 * @return TemporaryOption
	 */
	public static function getTemporary(?array $setting, CookieOption $cookie): TemporaryOption
	{
		$temporary = ArrayUtility::getOr($setting, 'temporary', null);
		$overwriteCookie = self::mergeCookie($cookie, $temporary);
		if (is_null($overwriteCookie->span)) {
			$overwriteCookie->span = new DateInterval('PT30M');
		}

		$option = TemporaryOption::create(
			ArrayUtility::getOr($temporary, 'name', 'TEMP'),
			ArrayUtility::getOr($temporary, 'save', './temp'),
			$overwriteCookie
		);

		return $option;
	}

	/**
	 * セッション設定を取得。
	 *
	 * @param array<string,string>|null $setting
	 * @param CookieOption $cookie
	 * @return SessionOption
	 */
	public static function getSession(?array $setting, CookieOption $cookie): SessionOption
	{
		$session = ArrayUtility::getOr($setting, 'session', null);
		$overwriteCookie = self::mergeCookie($cookie, $session);

		$option = SessionOption::create(
			ArrayUtility::getOr($session, 'name', 'PHPSESSID'),
			ArrayUtility::getOr($session, 'save', ''),
			$overwriteCookie
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
		$setting = ArrayUtility::getOr(AppConfiguration::$config, 'store', null);

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
