<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Store\SessionStore;

/**
 * アクションに対するオプション。
 */
class ActionOption
{
	private static ?ActionOption $none;
	public static function none(): ActionOption
	{
		return self::$none ??= new ActionOption();
	}

	public string $errorControllerName;

	/**
	 * フィルタリング処理
	 *
	 * @var null|(callable(FilterArgument $argument):(HttpStatus|array{status:HttpStatus}))
	 */
	public $filter;
}
