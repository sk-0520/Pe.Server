<?php

declare(strict_types=1);

namespace PeServer\Core\Collection;

use PeServer\Core\Throws\AccessKeyNotFoundException;

class Access
{
	#region function

	/**
	 * 値を取得する。
	 *
	 * @param array $array
	 * @phpstan-param array<mixed> $array
	 * @param array-key $key
	 * @return mixed
	 */
	public static function getRawValue(array $array, string|int $key): mixed
	{
		if (array_key_exists($key, $array)) {
			return $array[$key];
		}

		throw new AccessKeyNotFoundException($key);
	}

	#endregion
}
