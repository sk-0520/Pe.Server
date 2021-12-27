<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Store\SessionStore;

/**
 * アクションに対するオプション。
 */
class ActionOptions
{
	private static ?ActionOptions $none;
	public static function none(): ActionOptions
	{
		return self::$none ??= new ActionOptions();
	}

	public string $errorControllerName;

	/**
	 * フィルタリング処理
	 *
	 * @var null|(callable(FilterArgument $argument):HttpStatus)
	 */
	public $filter;

}
