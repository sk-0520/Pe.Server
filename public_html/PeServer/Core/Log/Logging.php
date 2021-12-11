<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

define('REQUEST_ID', bin2hex(openssl_random_pseudo_bytes(6)));

use \PeServer\Core\ArrayUtility;
use \PeServer\Core\ILogger;
use \PeServer\Core\InitializeChecker;
use \PeServer\Core\Log\FileLogger;
use \PeServer\Core\Log\MultiLogger;
use \PeServer\Core\StringUtility;

/**
 * ロガー生成処理。
 *
 * DIコンテナとか無いのでこいつを静的に使用してログがんばる。
 */
class Logging
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker
	 */
	private static $initializeChecker;

	private static $loggingConfiguration;

	private static $level;
	private static $format;

	public static function initialize(array $loggingConfiguration)
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$loggingConfiguration = $loggingConfiguration;

		self::$level = self::$loggingConfiguration['level'];
		self::$format = self::$loggingConfiguration['format'];
	}

	private static function createMessage($message, ...$parameters): string
	{
		if (is_null($message)) {
			if (ArrayUtility::isNullOrEmpty($parameters)) {
				return '';
			}
			return var_export($parameters, true);
		}

		if (is_string($message) && !ArrayUtility::isNullOrEmpty($parameters)) {
			return StringUtility::replaceMap($message, array_map(function ($value) {
				if (is_string($value)) {
					return $value;
				}
				return strval($value);
			}, $parameters));
		} else {
			if (ArrayUtility::isNullOrEmpty($parameters)) {
				return var_export($message, true);
			}
			return var_export(['message' => $message, 'parameters' => $parameters], true);
		}

		return $message;
	}

	public static function format(int $level, int $traceIndex, $message, ...$parameters): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		//DEBUG_BACKTRACE_PROVIDE_OBJECT
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$traceCaller = $backtrace[$traceIndex];
		$traceMethod = $backtrace[$traceIndex + 1];

		$map = [
			'TIMESTAMP' => date('c'),
			'IP' => ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', ''),
			'REQUEST_ID' => REQUEST_ID,
			'UA' => ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', ''),
			'METHOD' => ArrayUtility::getOr($_SERVER, 'REQUEST_METHOD', ''),
			'REQUEST' => ArrayUtility::getOr($_SERVER, 'REQUEST_URI', ''),
			'SESSION' => session_id(),
			//-------------------
			'FILE' => ArrayUtility::getOr($traceCaller, 'file', ''),
			'LINE' => ArrayUtility::getOr($traceCaller, 'line', ''),
			//'CLASS' => ArrayUtility::getOr($traceMethod, 'class', ''),
			'FUNCTION' => ArrayUtility::getOr($traceMethod, 'function', ''),
			//'ARGS' => ArrayUtility::getOr($traceMethod, 'args', ''),
			//-------------------
			'LEVEL' => $level,
			'MESSAGE' => self::createMessage($message, ...$parameters),
		];

		return StringUtility::replaceMap(self::$format, $map);
	}

	public static function create(string $header): ILogger
	{
		self::$initializeChecker->throwIfNotInitialize();

		$loggers = [
			new FileLogger($header, self::$level, 1, self::$loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$level, 0, $loggers);
	}
}
