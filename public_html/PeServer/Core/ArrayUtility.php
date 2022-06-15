<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\TypeException;

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
	 * @param mixed $fallbackValue 失敗時に返却される値。
	 * @return mixed 値。返却時にそれが成功しているか失敗しているかは不明なので厳密さが必要であれば tryGet を使用すること。
	 */
	public static function getOr(?array $array, int|string $key, mixed $fallbackValue)
	{
		if (!is_null($array) && isset($array[$key])) {
			$result = $array[$key];
			if (!is_null($result) && !is_null($fallbackValue)) { //@phpstan-ignore-line
				$resultType = gettype($result);
				$fallbackValueType = gettype($fallbackValue);
				if ($resultType !== $fallbackValueType) {
					throw new TypeException();
				}
			}

			return $result;
		}

		return $fallbackValue;
	}

	/**
	 * 配列から値を取得する。
	 *
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @param int|string $key キー。
	 * @param mixed $result 値を格納する変数。
	 * @return boolean 値が存在したか。
	 */
	public static function tryGet(?array $array, int|string $key, mixed &$result): bool
	{
		if (!is_null($array) && isset($array[$key])) {
			$result = $array[$key];
			return true;
		}

		return false;
	}

	/**
	 * 配列の件数を取得。
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

	/**
	 * 配列に指定要素が存在するか。
	 *
	 * @param array<mixed> $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	public static function contains(array $haystack, mixed $needle): bool
	{
		return array_search($needle, $haystack) !== false;
	}

	/**
	 * 配列に該当キーは存在するか。
	 *
	 * @param array<mixed> $haystack
	 * @param int|string $key
	 * @return bool
	 */
	public static function existsKey(array $haystack, int|string $key): bool
	{
		return array_key_exists($key, $haystack);
	}

	/**
	 * array_keys ラッパー。
	 *
	 * @param array<int|string,mixed> $array
	 * @return array<int|string>
	 */
	public static function getKeys(array $array): array
	{
		return array_keys($array);
	}

	/**
	 * array_values ラッパー。
	 *
	 * @param array<int|string,mixed> $array
	 * @return array<mixed>
	 */
	public static function getValues(array $array): array
	{
		return array_values($array);
	}

	/**
	 * in_array ラッパー。
	 *
	 * @param array<int|string,mixed> $haystack
	 * @param array<mixed> $needle
	 * @return boolean
	 */
	public static function in(array $haystack, array $needle): bool
	{
		return in_array($needle, $haystack, true);
	}
}
