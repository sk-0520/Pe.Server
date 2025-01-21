<?php

declare(strict_types=1);

namespace PeServer\Core\Collection;

use PeServer\Core\Text;
use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\TypeUtility;

class Access
{
	#region function

	/**
	 * 配列から生値を取得する。
	 *
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return mixed
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 */
	public static function getValue(array $array, string|int $key): mixed
	{
		if (array_key_exists($key, $array)) {
			return $array[$key];
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
	 * 配列から `bool` 値を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return bool
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getBool(array $array, string|int $key): bool
	{
		$value = self::getValue($array, $key);
		if (is_bool($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `int` 値を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return int
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getInteger(array $array, string|int $key): int
	{
		$value = self::getValue($array, $key);
		if (is_integer($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `float` 値を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return float
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getFloat(array $array, string|int $key): float
	{
		$value = self::getValue($array, $key);
		if (is_float($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `string` 値を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return string
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getString(array $array, string|int $key): string
	{
		$value = self::getValue($array, $key);
		if (is_string($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
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
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @param string|class-string<T> $className
	 * @return object
	 * @phpstan-return ($className is class-string<T> ? T: object)
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getObject(array $array, string|int $key, ?string $className = null): object
	{
		$value = self::getValue($array, $key);
		if (self::isObject($value, $className)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から配列を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return array<mixed>
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArray(array $array, string|int $key): array
	{
		$value = self::getValue($array, $key);
		if (is_array($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	/**
	 * 配列から `bool` 配列を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return bool[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfBool(array $array, string|int $key): array
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
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return int[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfInteger(array $array, string|int $key): array
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
	 * 配列から `float` 配列を取得。
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return float[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfFloat(array $array, string|int $key): array
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
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @return string[]
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfString(array $array, string|int $key): array
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
	 * 配列から `object` 配列を取得。
	 * @template T of object
	 * @param array<mixed> $array
	 * @param array-key $key
	 * @param null|string|class-string<T> $className
	 * @return object[]
	 * @phpstan-return ($className is class-string<T> ? T[]: object[])
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException 値の方が違う。
	 */
	public static function getArrayOfObject(array $array, string|int $key, ?string $className = null): array
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
