<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

define('REQUEST_ID', bin2hex(openssl_random_pseudo_bytes(6)));

use \PeServer\Core\ILogger;
use \PeServer\Core\Log\FileLogger;
use \PeServer\Core\Log\MultiLogger;
use PeServer\Core\StringUtility;

/**
 * ロガー生成処理。
 *
 * DIコンテナとか無いのでこいつを静的に使用してログがんばる。
 */
class Logging
{
	private static $loggingConfiguration;

	private static $level;
	private static $format;

	public static function initialize(array $loggingConfiguration)
	{
		self::$loggingConfiguration = $loggingConfiguration;

		self::$level = self::$loggingConfiguration['level'];
		self::$format = self::$loggingConfiguration['format'];
	}

	private static function createMessage($message, string ...$parameters): string
	{
		return '';
	}

	public static function format(int $level, int $traceIndex, $message, string ...$parameters): string
	{
		//DEBUG_BACKTRACE_PROVIDE_OBJECT
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$traceCaller = $backtrace[$traceIndex];
		$traceMethod = $backtrace[$traceIndex + 1];

		$map = [
			'TIMESTAMP' => date('c'),
			'IP' => @$_SERVER['REMOTE_ADDR'] ?: '',
			'REQUEST_ID' => REQUEST_ID,
			'UA' => @$_SERVER['HTTP_USER_AGENT'] ?: '',
			'METHOD' => @$_SERVER['REQUEST_METHOD'] ?: '',
			'REQUEST' => @$_SERVER['REQUEST_URI'] ?: '',
			'SESSION' => session_id(),
			//-------------------
			'FILE' => @$traceCaller['file'] ?: '',
			'LINE' => @$traceCaller['line'] ?: '',
			'FUNCTION' => @$traceMethod['function'] ?: '',
			'ARGS' => @$traceMethod['args'] ?: '',
			//-------------------
			'LEVEL' => $level,
			'MESSAGE' => $message,
		];

		return StringUtility::replaceMap(self::$format, $map);
	}

	public static function create(string $header): ILogger
	{
		$loggers = [
			new FileLogger($header, self::$level, 1, self::$loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$level, 0, $loggers);
	}
}
