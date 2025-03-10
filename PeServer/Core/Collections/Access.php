<?php

declare(strict_types=1);

namespace PeServer\Core\Collections;

use ArrayAccess;
use PeServer\Core\Text;
use PeServer\Core\Throws\AccessInvalidLogicalTypeException;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\TypeUtility;

/**
 * 配列から特定の型のデータ取得を行う。
 */
class Access
{
	#region function

	/**
	 * 配列(的なもの)から生値を取得する。
	 *
	 * `ArrayAccess` を渡した場合、 `array_key_exists` が使用できないため値が `null` の場合に少し挙動が違う点に注意。
	 *
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return mixed
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 */
	public static function getValue(array|ArrayAccess $array, string|int $key): mixed
	{
		if (is_array($array)) {
			if (array_key_exists($key, $array)) {
				return $array[$key];
			}
		} else {
			if (isset($array[$key])) {
				return $array[$key];
			}
		}

		throw new AccessKeyNotFoundException("key = $key");
	}

	/**
	 * `AccessValueTypeException` ヘルパー。
	 * @param string|int $key 添え字。
	 * @param mixed $value 値。
	 * @return never
	 * @throws AccessValueTypeException 値の方が違う。 ただこれを投げるだけ。
	 */
	private static function throwInvalidType(string|int $key, mixed $value): never
	{
		throw new AccessValueTypeException("$key is " . TypeUtility::getType($value));
	}

	/**
	 * `AccessInvalidLogicalTypeException` ヘルパー。
	 * @param string|int $key 添え字。
	 * @param mixed $value 値。
	 * @return never
	 * @throws AccessInvalidLogicalTypeException 値の方が違う。 ただこれを投げるだけ。
	 */
	private static function throwInvalidLogicalType(string|int $key, mixed $value): never
	{
		throw new AccessInvalidLogicalTypeException("$key is " . TypeUtility::getType($value));
	}

	/**
	 * 配列から `bool` 値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return bool
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getBool(array|ArrayAccess $array, string|int $key): bool
	{
		$value = self::getValue($array, $key);
		if (is_bool($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `int` 値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getInteger(array|ArrayAccess $array, string|int $key): int
	{
		$value = self::getValue($array, $key);
		if (is_integer($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から 0 を含む整数値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int
	 * @phpstan-return non-negative-int
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessInvalidLogicalTypeException 値の方が違う。
	 */
	public static function getUInteger(array|ArrayAccess $array, string|int $key): int
	{
		$result = self::getInteger($array, $key);
		if ($result < 0) {
			self::throwInvalidLogicalType($key, $result);
		}

		return $result;
	}

	/**
	 * 配列から 0 超過の整数値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int
	 * @phpstan-return positive-int
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessInvalidLogicalTypeException 値の方が違う。
	 */
	public static function getPositiveInteger(array|ArrayAccess $array, string|int $key): int
	{
		$result = self::getInteger($array, $key);
		if ($result <= 0) {
			self::throwInvalidLogicalType($key, $result);
		}

		return $result;
	}

	/**
	 * 配列から `float` 値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return float
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getFloat(array|ArrayAccess $array, string|int $key): float
	{
		$value = self::getValue($array, $key);
		if (is_float($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `string` 値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return string
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getString(array|ArrayAccess $array, string|int $key): string
	{
		$value = self::getValue($array, $key);
		if (is_string($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から 非空文字列 値を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @param bool $trim 文字列をトリムするか。
	 * @return non-empty-string
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getNonEmptyString(array|ArrayAccess $array, string|int $key, bool $trim = true): string
	{
		$value = self::getString($array, $key);
		if ($trim) {
			$value = Text::trim($value);
		}

		if (Text::isNullOrEmpty($value)) {
			self::throwInvalidLogicalType($key, $value);
		}

		return $value;
	}

	/**
	 * `object` 判定。
	 * @template T of object
	 * @param mixed $value
	 * @param null|string|class-string<T> $className
	 * @return bool
	 * @phpstan-assert-if-true ($className is class-string<T> ? T: object) $value
	 */
	private static function isObject(mixed $value, ?string $className): bool // @phpstan-ignore method.templateTypeNotInParameter
	{
		if (!is_object($value)) {
			return false;
		}

		if ($className === null) {
			return true;
		}

		return is_a($value, $className, true);
	}

	/**
	 * 配列から `object` 値を取得。
	 * @template T of object
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @param string|class-string<T> $className
	 * @return object
	 * @phpstan-return ($className is class-string<T> ? T: object)
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getObject(array|ArrayAccess $array, string|int $key, ?string $className = null): object
	{
		$value = self::getValue($array, $key);
		if (self::isObject($value, $className)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return array<mixed>
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArray(array|ArrayAccess $array, string|int $key): array
	{
		$value = self::getValue($array, $key);
		if (is_array($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `bool` 配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return bool[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfBool(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_bool($value)) {
				self::throwInvalidType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から `int` 配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfInteger(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_integer($value)) {
				self::throwInvalidType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から 0 を含む整数値配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int[]
	 * @phpstan-return non-negative-int[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfUInteger(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_integer($value)) {
				self::throwInvalidType($key, $value);
			}
			if ($value < 0) {
				self::throwInvalidLogicalType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から 0 超過の整数値配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return int[]
	 * @phpstan-return positive-int[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfPositiveInteger(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_integer($value)) {
				self::throwInvalidType($key, $value);
			}
			if ($value <= 0) {
				self::throwInvalidLogicalType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から `float` 配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return float[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfFloat(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_float($value)) {
				self::throwInvalidType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から `string` 配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @return string[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfString(array|ArrayAccess $array, string|int $key): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_string($value)) {
				self::throwInvalidType($key, $value);
			}
		}

		return $values;
	}

	/**
	 * 配列から 非空文字列 配列を取得。
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @param bool $trim 文字列をトリムするか。
	 * @return non-empty-string[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfNonEmptyString(array|ArrayAccess $array, string|int $key, bool $trim = true): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!is_string($value)) {
				self::throwInvalidType($key, $value);
			}
		}

		$result = [];
		foreach ($values as $key => $value) {
			if ($trim) {
				$value = Text::trim($value);
			}

			if (Text::isNullOrEmpty($value)) {
				self::throwInvalidLogicalType($key, $value);
			}

			$result[$key] = $value;
		}

		return $result;
	}

	/**
	 * 配列から `object` 配列を取得。
	 * @template T of object
	 * @param array<mixed>|ArrayAccess<array-key,mixed> $array
	 * @param array-key $key
	 * @param null|string|class-string<T> $className
	 * @return object[]
	 * @phpstan-return ($className is class-string<T> ? T[]: object[])
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfObject(array|ArrayAccess $array, string|int $key, ?string $className = null): array
	{
		$values = self::getArray($array, $key);
		foreach ($values as $key => $value) {
			if (!self::isObject($value, $className)) {
				self::throwInvalidType($key, $value);
			}
		}

		return $values;
	}

	#endregion
}
