<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\ArgumentException;

/**
 * セッション設定。
 */
class SessionOption
{
	public function __construct(string $name, string $savePath, CookieOption $cookie)
	{
		if (StringUtility::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}

		$this->name = $name;
		$this->savePath = $savePath;
		$this->cookie = $cookie;
	}

	public string $name;

	public CookieOption $cookie;

	public string $savePath;
}
