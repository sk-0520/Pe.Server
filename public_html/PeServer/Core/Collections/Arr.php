<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use PeServer\Core\Collections\OrderBy;
use PeServer\Core\Cryptography;
use PeServer\Core\ErrorHandler;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\TypeException;
use PeServer\Core\TypeUtility;

/**
 * 配列共通処理。
 *
 * 遅延処理が必要な場合 `Collections\Collection` を参照のこと。
 */
class Arr
{
	#region function

	/**
	 * 配列が `null` か空か。
	 *
	 * @param array<mixed>|null $array 対象配列。
	 * @return boolean `null` か空の場合に真。
	 * @phpstan-return ($array is null ? true: ($array is non-empty-array ? false: true))
	 * @phpstan-assert-if-false non-empty-array $array
	 */
	public static function isNullOrEmpty(?array $array): bool
	{
		if ($array === null) {
			return true;
		}

		return empty($array);
	}

	/**
	 * 配列から値を取得する。
	 *
	 * @template TValue
	 * @param array<int|string,mixed>|null $array 対象配列。
	 * @phpstan-param array<array-key,mixed|TValue>|null $array
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
		if ($array !== null && isset($array[$key])) {
			$result = $array[$key];
			if ($result !== null && $fallbackValue !== null) { //@phpstan-ignore-line
				$resultType = TypeUtility::getType($result);
				$fallbackValueType = TypeUtility::getType($fallbackValue);
				if ($resultType === $fallbackValueType) {
					return $result;
				}
				if (is_a($result, $fallbackValueType)) {
					/** @var TValue */
					return $result;
				}

				throw new TypeException();
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
		if ($array !== null && isset($array[$key])) {
			$result = $array[$key];
			return true;
		}

		return false;
	}

	/**
	 * 配列の件数を取得。
	 *
	 * @param array<mixed>|null $array 対象配列。
	 * @return int 件数。
	 * @phpstan-return UnsignedIntegerAlias
	 * @see https://www.php.net/manual/function.count.php
	 */
	public static function getCount(?array $array): int
	{
		if ($array === null) {
			return 0;
		}

		return count($array);
	}

	/**
	 * 配列に該当キーは存在するか。
	 *
	 * @param array<mixed> $haystack 対象配列。
	 * @phpstan-param array<array-key,mixed> $haystack
	 * @param int|string $key キー。
	 * @phpstan-param array-key $key
	 * @return bool
	 * @see https://www.php.net/manual/function.array-key-exists.php
	 */
	public static function containsKey(array $haystack, int|string $key): bool
	{
		return array_key_exists($key, $haystack);
	}

	/**
	 * 配列に指定要素が存在するか。
	 *
	 * @template TValue
	 * @param array<mixed> $haystack 対象配列。
	 * @phpstan-param TValue[] $haystack
	 * @param mixed $needle 検索データ。
	 * @phpstan-param TValue $needle
	 * @return boolean
	 * @see https://www.php.net/manual/function.array-search.php
	 */
	public static function containsValue(array $haystack, mixed $needle): bool
	{
		return array_search($needle, $haystack) !== false;
	}

	/**
	 * `array_keys` ラッパー。
	 *
	 * @param array<int|string,mixed> $array 対象配列。
	 * @phpstan-param array<array-key,mixed> $array
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
	 * @param array<int|string,mixed> $array 対象配列。
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
	 * @param array<int|string,mixed> $haystack 対象配列。
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
	 * @param array<mixed> $array 対象配列。
	 * @return int|string
	 * @phpstan-return array-key
	 * @see https://www.php.net/manual/function.array-key-first.php
	 * @throws KeyNotFoundException
	 */
	public static function getFirstKey(array $array): int|string
	{
		$result = array_key_first($array);
		if ($result === null) {
			throw new KeyNotFoundException();
		}

		return $result;
	}

	/**
	 * `array_key_last` ラッパー。
	 *
	 * @param array<mixed> $array 対象配列。
	 * @return int|string
	 * @phpstan-return array-key
	 * @see https://www.php.net/manual/function.array-key-last.php
	 * @throws KeyNotFoundException
	 */
	public static function getLastKey(array $array): int|string
	{
		$result = array_key_last($array);
		if ($result === null) {
			throw new KeyNotFoundException();
		}

		return $result;
	}

	/**
	 * `array_is_list` ラッパー。
	 *
	 * @param array<mixed> $array 対象配列。
	 * @return bool
	 * @see https://www.php.net/manual/function.array-is-list.php#127044
	 */
	public static function isList(array $array): bool
	{
		$function = 'array_is_list'; // ignore intelephense(1010)
		if (function_exists($function)) {
			return $function($array);
		}

		// https://www.php.net/manual/function.array-is-list.php#127044
		$i = 0;
		foreach ($array as $k => $v) {
			if ($k !== $i++) {
				return false;
			}
		}
		return true;
	}

	/**
	 * `array_unique` ラッパー。
	 *
	 * @template TKey of array-key
	 * @template TValue
	 * @param array<mixed> $array 対象配列。
	 * @phpstan-param array<TKey,TValue> $array
	 * @return array<mixed>
	 * @see https://www.php.net/manual/function.array-unique.php
	 */
	public static function toUnique(array $array): array
	{
		return array_unique($array, SORT_REGULAR);
	}

	/**
	 * `array_replace(_recursive)` ラッパー。
	 *
	 * @param array<mixed> $base 元になる配列。
	 * @param array<mixed> $overwrite 上書きする配列。
	 * @param bool $recursive 再帰的置き換えを行うか(`_recursive`呼び出し)。
	 * @return array<mixed>
	 * @see https://www.php.net/manual/function.array-replace-recursive.php
	 * @see https://www.php.net/manual/function.array-replace.php
	 */
	public static function replace(array $base, array $overwrite, bool $recursive = true): array
	{
		if ($recursive) {
			return array_replace_recursive($base, $overwrite);
		}

		return array_replace($base, $overwrite);
	}

	/**
	 * キー項目をランダム取得。
	 *
	 * @param array<mixed> $array 対象配列。
	 * @phpstan-param non-empty-array<mixed> $array
	 * @param int $count 取得数。
	 * @phpstan-param positive-int $count
	 * @return array<string|int>
	 * @phpstan-return array-key[]
	 * @throws ArgumentException
	 */
	public static function getRandomKeys(array $array, int $count): array
	{
		if ($count < 1) { //@phpstan-ignore-line
			throw new ArgumentException('$count');
		}

		$length = self::getCount($array);
		if ($length < $count) {
			throw new ArgumentException('$length < $count');
		}

		$result = [];
		$keys = self::getKeys($array);
		for ($i = 0; $i < $count; $i++) {
			$index = Cryptography::generateRandomInteger($length - 1);
			$result[] = $keys[$index];
		}

		return $result;
	}

	/**
	 * `reverse` ラッパー。
	 *
	 * @template TKey of array-key
	 * @template TValue
	 * @param array<mixed> $input 対象配列。
	 * @phpstan-param array<TKey,TValue> $input
	 * @return array<mixed>
	 * @phpstan-return array<TKey,TValue>
	 * @see https://www.php.net/manual/function.array-reverse.php
	 */
	public static function reverse(array $input): array
	{
		return array_reverse($input);
	}

	/**
	 * `array_flip` ラッパー。
	 *
	 * @template TKey of array-key
	 * @template TValue
	 * @param array<mixed> $input 対象配列。
	 * @phpstan-param array<TKey,TValue> $input
	 * @return array<mixed>
	 * @phpstan-param array<TValue,TKey> $input
	 * @throws ArgumentException
	 * @see https://www.php.net/manual/function.array-flip.php
	 */
	public static function flip(array $input): array
	{
		$result = ErrorHandler::trapError(fn () => array_flip($input));
		if (!$result->success) {
			throw new ArgumentException();
		}
		return $result->value;
	}

	/**
	 * `array_map` 的なことを行う非ラッパー処理。
	 *
	 * `array_map` がもうなんか順序もコールバック引数も何もかも怖い。
	 *
	 * @template TValue
	 * @template TResult
	 * @param array<mixed> $input 対象配列。
	 * @phpstan-param array<array-key,TValue> $input
	 * @param callable $callback
	 * @phpstan-param callable(TValue,array-key): TResult $callback
	 * @return array<mixed>
	 * @phpstan-return array<array-key,TResult>
	 */
	public static function map(array $input, callable $callback): array
	{
		/** @phpstan-var array<array-key,TResult> */
		$result = [];

		foreach ($input as $key => $value) {
			$result[$key] = $callback($value, $key);
		}

		return $result;
	}

	/**
	 * 指定した範囲内の整数から配列生成。
	 *
	 * PHP の `range` とは指定方法が違うことに注意。
	 *
	 * @param int $start 開始。
	 * @param int $count 件数。
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return self
	 * @phpstan-return array<int>
	 */
	public static function range(int $start, int $count): array
	{
		if ($count < 0) { //@phpstan-ignore-line
			throw new ArgumentException('$count');
		}

		if ($count === 0) {
			return [];
		}

		return \range($start, $start + $count - 1);
	}

	/**
	 * 繰り返される配列を生成。
	 *
	 * `array_fill` ラッパー。
	 *
	 * @template TRepeatValue
	 * @param mixed $value 値。
	 * @phpstan-param TRepeatValue $value
	 * @param int $count 件数。
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return self
	 * @phpstan-return TRepeatValue[]
	 * @see https://www.php.net/manual/function.array-fill.php
	 */
	public static function repeat(mixed $value, int $count): array
	{
		if ($count < 0) { //@phpstan-ignore-line
			throw new ArgumentException('$count');
		}

		return array_fill(0, $count, $value);
	}

	/**
	 * 値による単純ソート。
	 *
	 * @template TValue
	 * @param array $array
	 * @phpstan-param TValue[] $array
	 * @param int $orderBy
	 * @phpstan-param OrderBy::* $orderBy
	 * @return array
	 * @phpstan-return TValue[]
	 * @see https://www.php.net/manual/function.sort.php
	 * @see https://www.php.net/manual/function.rsort.php
	 */
	public static function sortByValue(array $array, int $orderBy): array
	{
		$result = $array;
		$flags = SORT_REGULAR;

		match ($orderBy) {
			OrderBy::ASCENDING => sort($result, $flags),
			OrderBy::DESCENDING => rsort($result, $flags),
		};

		return $result;
	}

	/**
	 * キーによる単純ソート。
	 *
	 * @template TValue
	 * @param array $array
	 * @phpstan-param array<array-key,TValue> $array
	 * @param int $orderBy
	 * @phpstan-param OrderBy::* $orderBy
	 * @return array
	 * @phpstan-return array<array-key,TValue>
	 * @see https://www.php.net/manual/function.ksort.php
	 * @see https://www.php.net/manual/function.krsort.php
	 */
	public static function sortByKey(array $array, int $orderBy): array
	{
		$result = $array;
		$flags = SORT_REGULAR;

		match ($orderBy) {
			OrderBy::ASCENDING => ksort($result, $flags),
			OrderBy::DESCENDING => krsort($result, $flags),
		};

		return $result;
	}

	/**
	 * 値による自然昇順ソート。
	 *
	 * @template TValue
	 * @param array $array
	 * @phpstan-param TValue[] $array
	 * @param bool $ignoreCase 大文字小文字を無視するか。
	 * @return array
	 * @phpstan-return TValue[]
	 * @see https://www.php.net/manual/function.natsort.php
	 * @see https://www.php.net/manual/function.natcasesort.php
	 */
	public static function sortNaturalByValue(array $array, bool $ignoreCase): array
	{
		$result = self::getValues($array);

		if ($ignoreCase) {
			natcasesort($result);
		} else {
			natsort($result);
		}

		return self::getValues($result);
	}

	/**
	 * 値によるユーザー定義ソート。
	 *
	 * `asort` とかもこいつでやってくれ。
	 *
	 * @template TValue
	 * @param array $array
	 * @phpstan-param TValue[] $array
	 * @param callable $callback
	 * @phpstan-param callable(TValue,TValue):int $callback
	 * @return array
	 * @phpstan-return TValue[]
	 * @see https://www.php.net/manual/function.usort.php
	 * @see https://www.php.net/manual/function.uasort.php
	 * @see https://www.php.net/manual/function.asort.php
	 * @see https://www.php.net/manual/function.arsort.php
	 */
	public static function sortCallbackByValue(array $array, callable $callback): array
	{
		$result = $array;

		usort($result, $callback);

		return $result;
	}

	/**
	 * キーによるユーザー定義ソート。
	 *
	 * @template TValue
	 * @param array $array
	 * @phpstan-param array<array-key,TValue> $array
	 * @param callable $callback
	 * @phpstan-param callable(array-key,array-key):int $callback
	 * @return array
	 * @phpstan-return array<array-key,TValue>
	 * @see https://www.php.net/manual/function.usort.php
	 */
	public static function sortCallbackByKey(array $array, callable $callback): array
	{
		$result = $array;

		uksort($result, $callback);

		return $result;
	}

	// いやこれ無理
	// public static function multisort(array $array, callable $callback): array
	// {
	// }

	#endregion
}
