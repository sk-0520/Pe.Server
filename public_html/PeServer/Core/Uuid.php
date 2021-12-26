<?php

declare(strict_types=1);

namespace PeServer\Core;


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
			'%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
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
}
