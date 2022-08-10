<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Text;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\ArgumentException;

/**
 * 一時データ設定。
 *
 * @immutable
 */
class TemporaryOption
{
	/** 一時データID保持Cookie名。 */
	public const DEFAULT_NAME = 'PHPTEMPID';

	public function __construct(
		public string $name,
		public string $savePath,
		public CookieOption $cookie
	) {
		if (Text::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}
	}
}
