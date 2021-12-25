<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;

/**
 * アクションに対するオプション。
 */
class ActionOptions
{

	private ?ActionOptions $_none;
	public static function none(): ActionOptions
	{
		return self::$_none ??= new ActionOptions();
	}
}
