<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\InvalidOperationException;
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

		return self::getCount($array) === 0;
	}

	/**
	 * 配列から値を取得する。
	 *
	 * @template TValue
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @phpstan-param array<array-key,TValue>|null $array
	 * @param int|string $key キー。
	 * @phpstan-param array-key $key
	 * @param mixed $fallbackValue 失敗時に返却される値。
	 * @phpstan-param TValue $fallbackValue
	 * @return mixed 値。返却時にそれが成功しているか失敗しているかは不明なので厳密さが必要であれば tryGet を使用すること。
	 * @phpstan-return TValue
	 * @throws TypeException
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
	 * @template TValue
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @phpstan-param array<array-key,TValue>|null $array
	 * @param int|string $key キー。
	 * @phpstan-param array-key $key
	 * @param mixed $result 値を格納する変数。
	 * @phpstan-param TValue $result
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
	 * @see https://www.php.net/manual/function.count.php
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
	 * @template TValue
	 * @param array<mixed> $haystack
	 * @phpstan-param TValue[] $haystack
	 * @param mixed $needle
	 * @phpstan-param TValue $needle
	 * @return boolean
	 * @see https://www.php.net/manual/function.array-search.php
	 */
	public static function contains(array $haystack, mixed $needle): bool
	{
		return array_search($needle, $haystack) !== false;
	}

	/**
	 * 配列に該当キーは存在するか。
	 *
	 * @param array<mixed> $haystack
	 * @phpstan-param array<array-key,mixed> $haystack
	 * @param int|string $key
	 * @phpstan-param array-key $key
	 * @return bool
	 * @see https://www.php.net/manual/function.array-key-exists.php
	 */
	public static function existsKey(array $haystack, int|string $key): bool
	{
		return array_key_exists($key, $haystack);
	}

	/**
	 * `array_keys` ラッパー。
	 *
	 * @template TValue
	 * @param array<int|string,mixed> $array
	 * @phpstan-param array<array-key,TValue> $array
	 * @return array<int|string>
	 * @phpstan-return array-key[]
	 * @see https://www.php.net/manual/function.array-keys.php
	 */
	public static function getKeys(array $array): array
	{
		return array_keys($array);
	}

	/**
	 * `array_values` ラッパー。
	 *
	 * @template TValue
	 * @param array<int|string,mixed> $array
	 * @phpstan-param array<array-key,TValue> $array
	 * @return array<mixed>
	 * @phpstan-return TValue[]
	 * @see https://www.php.net/manual/function.array-values.php
	 */
	public static function getValues(array $array): array
	{
		return array_values($array);
	}

	/**
	 * `in_array` ラッパー。
	 *
	 * @template TValue
	 * @param array<int|string,mixed> $haystack
	 * @phpstan-param array<array-key,TValue> $haystack
	 * @param mixed $needle
	 * @phpstan-param TValue $needle
	 * @return boolean
	 * @see https://www.php.net/manual/function.in-array.php
	 */
	public static function in(array $haystack, mixed $needle): bool
	{
		return in_array($needle, $haystack, true);
	}

	/**
	 * `array_key_first` ラッパー。
	 *
	 * @param array<mixed> $array
	 * @return int|string
	 * @see https://www.php.net/manual/function.array-key-first.php
	 */
	public static function getFirstKey(array $array): int|string
	{
		$result = array_key_first($array);
		if (is_null($result)) {
			throw new InvalidOperationException();
		}

		return $result;
	}

	/**
	 * `array_key_last` ラッパー。
	 *
	 * @param array<mixed> $array
	 * @return int|string
	 * @see https://www.php.net/manual/function.array-key-last.php
	 */
	public static function getLastKey(array $array): int|string
	{
		$result = array_key_last($array);
		if (is_null($result)) {
			throw new InvalidOperationException();
		}

		return $result;
	}

	/**
	 * `array_is_list` ラッパー。
	 *
	 * @param array $array
	 * @return bool
	 * @see https://www.php.net/manual/function.array-is-list.php#127044
	 */
	public static function isList(array $array): bool
	{
		if (function_exists('array_is_list')) {
			$function = 'array_is_list'; // ignore intelephense(1010)
			return $function($array);
		}

		// https://www.php.net/manual/ja/function.array-is-list.php#127044
		$i = 0;
		foreach ($array as $k => $v) {
			if ($k !== $i++) {
				return false;
			}
		}
		return true;
	}
}
