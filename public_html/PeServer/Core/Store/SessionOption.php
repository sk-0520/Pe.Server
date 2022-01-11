<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\ArgumentException;

class SessionOption
{
	public static function create(string $name, string $savePath, CookieOption $cookie): SessionOption
	{
		if (StringUtility::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}

		$option = new SessionOption();

		$option->name = $name;
		$option->savePath = $savePath;
		$option->cookie = $cookie;

		return $option;
	}

	public string $name;

	public CookieOption $cookie;

	public string $savePath;
}
