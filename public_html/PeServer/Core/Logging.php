<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\ILogger;
use \PeServer\Core\Logger;

/**
 * ロガー生成処理。
 *
 * DIコンテナとか無いのでこいつを静的に使用してログがんばる。
 */
class Logging
{
	private static $loggingConfiguration;

	public static $level;

	public static function initialize(array $loggingConfiguration)
	{
		self::$loggingConfiguration = $loggingConfiguration;

		self::$level = self::$loggingConfiguration['level'];
	}

	public static function create(string $header): ILogger
	{
		return new Logger($header, self::$level);
	}
}