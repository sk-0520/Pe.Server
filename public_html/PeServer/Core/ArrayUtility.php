<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 配列共通処理。
 */
class ArrayUtility
{
	/**
	 * 配列がnullか空か。
	 *
	 * @param array<mixed>|null $array
	 * @return boolean
	 */
	public static function isNullOrEmpty(?array $array): bool
	{
		if (is_null($array)) {
			return true;
		}

		return count($array) === 0;
	}

	/**
	 * 配列から値を取得する。
	 *
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @param int|string $key キー。
	 * @param mixed $defaultValue 失敗時に返却される値。
	 * @return mixed 値。返却時にそれが成功しているか失敗しているかは不明なので厳密さが必要であれば tryGet を使用すること。
	 */
	public static function getOr(?array $array, $key, $defaultValue)
	{
		if (!is_null($array) && isset($array[$key])) {
			return $array[$key];
		}

		return $defaultValue;
	}

	/**
	 * 配列から値を取得する。
	 *
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @param int|string $key キー。
	 * @param mixed $result 値を格納する変数。
	 * @return boolean 値が存在したか。
	 */
	public static function tryGet(?array $array, $key, &$result): bool
	{
		if (!is_null($array) && isset($array[$key])) {
			$result = $array[$key];
			return true;
		}

		return false;
	}

	/**
	 * Undocumented function
	 *
	 * @param array<mixed>|null $array
	 * @return int
	 */
	public static function getCount(?array $array): int
	{
		if (is_null($array)) {
			return 0;
		}

		return count($array);
	}

	public static function contains(array $haystack, mixed $needle): bool
	{
		return array_search($needle, $haystack) !== false;
	}
}
