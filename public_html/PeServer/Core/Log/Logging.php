<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

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

	public static function format(int $level, int $traceIndex, string $message, ?array $parameters = null)
	{
		//DEBUG_BACKTRACE_PROVIDE_OBJECT
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$trace = $backtrace[$traceIndex + 1];

		//"{TIMESTAMP} {METHOD} {REQUEST} {IP} {UA} {SESSION} {FILE} {LINE} {FUNCTION} {ARGS} {MESSAGE}",
		$map = [
			'TIMESTAMP' => date('Y-m-d\TH:i:s.vP'),
			'METHOD' => @$_SERVER['REQUEST_METHOD'] ?: '',
			'REQUEST' => @$_SERVER['REQUEST_URI'] ?: '',
			'IP' => @$_SERVER['REMOTE_ADDR'] ?: '',
			'UA' => @$_SERVER['HTTP_USER_AGENT'] ?: '',
			'SESSION' => session_id(),
			//-------------------
			'FILE' => @$trace['file'] ?: '',
			'LINE' => @$trace['line'] ?: '',
			'FUNCTION' => @$trace['function'] ?: '',
			'ARGS' => @$trace['args'] ?: '',
			//-------------------
			'MESSAGE' => $message,
		];

		return StringUtility::replaceMap(self::$format, $map);
	}

	public static function create(string $header): ILogger
	{
		$loggers = [
			new FileLogger($header, self::$level, NULL, self::$loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$level, self::class . '::format', $loggers);
	}
}
