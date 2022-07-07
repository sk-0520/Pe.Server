<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;

/**
 * $_SERVER, $_COOKIE, $_SESSION 読み込みアクセス。
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class SpecialStore
{
	/**
	 * $_SERVER から値取得。
	 *
	 * @template TValue
	 * @param string $name インデックス名。
	 * @param mixed $fallbackValue 取得時失敗時の値。
	 * @phpstan-param TValue $fallbackValue
	 * @return mixed
	 * @phpstan-return TValue
	 */
	public function getServer(string $name, mixed $fallbackValue = InitialValue::EMPTY_STRING): mixed
	{
		$result = ArrayUtility::getOr($_SERVER, $name, $fallbackValue);
		return $result;
	}

	/**
	 * $_SERVER に名前が存在するか。
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function containsServerName(string $name): bool
	{
		return isset($_SERVER[$name]);
	}

	/**
	 * $_COOKIE から値取得。
	 *
	 * @param string $name
	 * @param string $fallbackValue
	 * @return string
	 */
	public function getCookie(string $name, string $fallbackValue = InitialValue::EMPTY_STRING): string
	{
		$result = ArrayUtility::getOr($_SERVER, $name, $fallbackValue);
		return $result;
	}
}
