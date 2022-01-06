<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

abstract class Uuid
{
	/**
	 * GUID生成。
	 *
	 * @return string GUID文字列
	 */
	public static function generateGuid(): string
	{
		if (function_exists('com_create_guid') === true) {
			$guid = com_create_guid();
			if ($guid !== false) {
				return StringUtility::trim($guid, '{}');
			}
		}

		return sprintf(
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(16384, 20479),
			mt_rand(32768, 49151),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535)
		);
	}

	public static function isEqualGuid(string $a, string $b): bool
	{
		if ($a === $b) {
			return true;
		}

		$a = StringUtility::trim($a, '{}');
		$b = StringUtility::trim($b, '{}');

		if ($a === $b) {
			return true;
		}

		$a = StringUtility::toLower($a);
		$b = StringUtility::toLower($b);

		if ($a === $b) {
			return true;
		}

		$a = StringUtility::replace($a, '-', '');
		$b = StringUtility::replace($b, '-', '');

		if ($a === $b) {
			return true;
		}

		return false;
	}

	/**
	 * GUIDとして正しい書式か。
	 *
	 * 正確には正しくなくとも補正してGUIDとして扱えるか。
	 *
	 * @param string $value
	 * @return boolean
	 */
	public static function isGuid(string $value): bool
	{
		return Regex::isMatch($value, '/^\{?[a-fA-F0-9]{8}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{12}\}?$/');
	}

	/**
	 * GUIDの文字列表現を統一。
	 *
	 * @param string $value
	 * @return string
	 * @throws ArgumentException 変換失敗。
	 */
	public static function adjustGuid(string $value): string
	{
		if (StringUtility::isNullOrWhiteSpace($value)) {
			throw new ArgumentException();
		}

		$a = StringUtility::trim($value, '{}');
		$b = StringUtility::replace($a, '-', '');
		if (StringUtility::getLength($b) !== 32) {
			throw new ArgumentException();
		}
		$c = StringUtility::toLower($b);
		$d = [
			StringUtility::substring($c, 0, 8),
			StringUtility::substring($c, 8, 4),
			StringUtility::substring($c, 8 + 4, 4),
			StringUtility::substring($c, 8 + 4 + 4, 4),
			StringUtility::substring($c, 8 + 4 + 4 + 4, 12),
		];
		$e = StringUtility::join($d, '-');

		if (!self::isGuid($e)) {
			throw new ArgumentException();
		}

		return $e;
	}
}
