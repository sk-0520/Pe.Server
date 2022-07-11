<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use DateInterval;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Store\SpecialStore;

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
		/** @var array<string,mixed> */
		$cookie = ArrayUtility::getOr($setting, 'cookie', []);
		$overwriteSetting = [
			'cookie' => array_replace_recursive($baseSetting, $cookie),
		];

		$overwriteCookie = self::getCookie($overwriteSetting);

		return $overwriteCookie;
	}

	/**
	 * クッキー設定を取得。
	 *
	 * @param array<string,array<string,string|bool>>|null $setting
	 * @return CookieOption
	 */
	public static function getCookie(?array $setting): CookieOption
	{
		/** @var array<string,mixed>|null */
		$cookie = ArrayUtility::getOr($setting, 'cookie', null);

		/** @var string|null */
		$spanSetting = ArrayUtility::getOr($cookie, 'span', null);
		/** @var DateInterval|null */
		$span = null;
		if (!is_null($spanSetting)) {
			$span = new DateInterval($spanSetting);
		}

		/** @var string */
		$path = ArrayUtility::getOr($cookie, 'path', '/');
		/** @var bool */
		$secure = ArrayUtility::getOr($cookie, 'secure', false);
		/** @var bool */
		$httpOnly = ArrayUtility::getOr($cookie, 'httpOnly', true);
		/** @var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict' */
		$sameSite = ArrayUtility::getOr($cookie, 'sameSite', 'None');
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
	 * @param array<string,array<string,string|bool>>|null $setting
	 * @param CookieOption $cookie
	 * @return TemporaryOption
	 */
	public static function getTemporary(?array $setting, CookieOption $cookie): TemporaryOption
	{
		/** @var array<string,mixed>|null */
		$temporary = ArrayUtility::getOr($setting, 'temporary', null);
		$overwriteCookie = self::mergeCookie($cookie, $temporary);
		if (is_null($overwriteCookie->span)) {
			$overwriteCookie->span = new DateInterval('PT30M');
		}

		/** @var string */
		$name = ArrayUtility::getOr($temporary, 'name', 'TEMP');
		/** @var string */
		$save = ArrayUtility::getOr($temporary, 'save', './temp');
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
	 * @param array<string,array<string,string|bool>>|null $setting
	 * @param CookieOption $cookie
	 * @return SessionOption
	 */
	public static function getSession(?array $setting, CookieOption $cookie): SessionOption
	{
		/** @var array<string,mixed>|null */
		$session = ArrayUtility::getOr($setting, 'session', null);
		$overwriteCookie = self::mergeCookie($cookie, $session);

		/** @var string */
		$name = ArrayUtility::getOr($session, 'name', 'PHPSESSID');
		/** @var string */
		$save = ArrayUtility::getOr($session, 'save', '');
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
	 * @param array<string,array<string,string|bool>>|null $setting;
	 * @return StoreOptions
	 */
	public static function build($setting): StoreOptions
	{
		$cookie = self::getCookie($setting);
		$temporary = self::getTemporary($setting, $cookie);
		$session = self::getSession($setting, $cookie);

		return new StoreOptions(
			$cookie,
			$temporary,
			$session
		);
	}
}
