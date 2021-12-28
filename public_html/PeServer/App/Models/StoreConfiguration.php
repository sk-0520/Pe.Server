<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use \PeServer\Core\ArrayUtility;
use \PeServer\Core\Store\CookieOption;

abstract class StoreConfiguration
{
	public static function cookie(): CookieOption
	{
		$store = ArrayUtility::getOr(AppConfiguration::$json, 'store', null);
		$cookie = ArrayUtility::getOr($store, 'cookie', null);

		$option = new CookieOption(
			ArrayUtility::getOr($cookie, 'path', '/'),
			ArrayUtility::getOr($cookie, 'span', null),
			ArrayUtility::getOr($cookie, 'secure', false),
			ArrayUtility::getOr($cookie, 'httpOnly', true)
		);

		return $option;
	}
}
