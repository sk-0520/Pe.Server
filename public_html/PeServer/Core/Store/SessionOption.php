<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \PeServer\Core\Store\CookieOption;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Throws\ArgumentException;

class SessionOption
{
	public static function create(string $name, CookieOption $cookie): SessionOption
	{
		if (StringUtility::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}

		$option = new self();

		$option->name = $name;
		$option->cookie = $cookie;

		return $option;
	}

	public string $name;

	public CookieOption $cookie;

	public string $savePath;
}
