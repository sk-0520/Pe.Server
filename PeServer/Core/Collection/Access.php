<?php

declare(strict_types=1);

namespace PeServer\Core\Collection;

use PeServer\Core\Throws\AccessKeyNotFoundException;
use PeServer\Core\Throws\AccessValueTypeException;
use PeServer\Core\TypeUtility;

class Access
{
	#region function

	/**
	 * 生値を取得する。
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

	private static function throwInvalidType(string|int $key, mixed $value): never
	{
		throw new AccessValueTypeException("$key is " . TypeUtility::getType($value));
	}

	/**
	 *
	 * @param array<mixed> $array
	 * @param string|int $key
	 * @param array-key $key
	 * @return int
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException
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
	 *
	 * @param array<mixed> $array
	 * @param string|int $key
	 * @param array-key $key
	 * @return float
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException
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
	 *
	 * @param array<mixed> $array
	 * @param string|int $key
	 * @param array-key $key
	 * @return string
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException
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
	 *
	 * @param array<mixed> $array
	 * @param string|int $key
	 * @param array-key $key
	 * @return array<mixed>
	 * @throws AccessKeyNotFoundException キーが見つからない。
	 * @throws AccessValueTypeException
	 */
	public static function getArray(array $array, string|int $key): array
	{
		$value = self::getValue($array, $key);
		if (is_array($value)) {
			return $value;
		}

		self::throwInvalidType($key, $value);
	}

	#endregion
}
