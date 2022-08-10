<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\DefaultValue;
use PeServer\Core\Throws\ArgumentException;

abstract class Uuid
{
	/**
	 * GUID生成。
	 *
	 * 非Windows環境は謎の何か。
	 *
	 * @return string GUID文字列
	 */
	public static function generateGuid(): string
	{
		if (function_exists('com_create_guid')) {
			$guid = com_create_guid();
			if ($guid !== false) {
				return Text::trim($guid, '{}');
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

		$a = Text::trim($a, '{}');
		$b = Text::trim($b, '{}');

		if ($a === $b) {
			return true;
		}

		$a = Text::toLower($a);
		$b = Text::toLower($b);

		if ($a === $b) {
			return true;
		}

		$a = Text::replace($a, '-', DefaultValue::EMPTY_STRING);
		$b = Text::replace($b, '-', DefaultValue::EMPTY_STRING);

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
		$regex = new Regex();
		return $regex->isMatch($value, '/^\{?[a-fA-F0-9]{8}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{4}-?[a-fA-F0-9]{12}\}?$/');
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
		if (Text::isNullOrWhiteSpace($value)) {
			throw new ArgumentException();
		}

		$a = Text::trim($value, '{}');
		$b = Text::replace($a, '-', DefaultValue::EMPTY_STRING);
		if (Text::getLength($b) !== 32) {
			throw new ArgumentException();
		}
		$c = Text::toLower($b);
		$d = [
			Text::substring($c, 0, 8),
			Text::substring($c, 8, 4),
			Text::substring($c, 8 + 4, 4),
			Text::substring($c, 8 + 4 + 4, 4),
			Text::substring($c, 8 + 4 + 4 + 4, 12),
		];
		$e = Text::join('-', $d);

		if (!self::isGuid($e)) {
			throw new ArgumentException();
		}

		return $e;
	}
}
