<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\ArgumentException;

/**
 * 一時データ設定。
 */
class TemporaryOption
{
	public function __construct(
		/** @readonly */
		public string $name,
		/** @readonly */
		public string $savePath,
		/** @readonly */
		public CookieOption $cookie
	) {
		if (StringUtility::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}
	}
}
