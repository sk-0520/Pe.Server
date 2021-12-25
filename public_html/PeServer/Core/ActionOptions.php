<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Mvc\SessionStore;

/**
 * アクションに対するオプション。
 */
class ActionOptions
{
	private static ?ActionOptions $_none;
	public static function none(): ActionOptions
	{
		return self::$_none ??= new ActionOptions();
	}

	public string $errorControllerName;

	/**
	 * Undocumented variable
	 *
	 * @var callable(SessionStore):HttpStatus |null
	 */
	public $sessionFilter;
}
