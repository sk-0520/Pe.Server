<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

define('LOG_REQUEST_ID', bin2hex(openssl_random_pseudo_bytes(6)));

use \LogicException;
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
	 * @var InitializeChecker|null
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

	private static function formatLevel(int $level): string
	{
		switch ($level) {
			case ILogger::LEVEL_TRACE:
				return 'TRACE';
			case ILogger::LEVEL_DEBUG:
				return 'DEBUG';
			case ILogger::LEVEL_INFORMATION:
				return 'INFO ';
			case ILogger::LEVEL_WARNING:
				return 'WARN ';
			case ILogger::LEVEL_ERROR:
				return 'ERROR';
		}

		throw new LogicException("log level: $level");
	}

	private static function formatMessage($message, ...$parameters): string
	{
		if (is_null($message)) {
			if (ArrayUtility::isNullOrEmpty($parameters)) {
				return '';
			}
			return var_export($parameters, true);
		}

		if (is_string($message) && !ArrayUtility::isNullOrEmpty($parameters) && array_keys($parameters)[0] === 0) {
			$values = array_map(function ($value) {
				if (is_string($value)) {
					return $value;
				}
				if(is_object($value) || is_array($value)) {
					return var_export($value, true);
				}

				return strval($value);
			}, $parameters);

			/** @var array(string,string) */
			$map = [];
			foreach ($values as $key => $value) {
				$map[strval($key)] = $value;
			}

			return StringUtility::replaceMap($message, $map);
		}

		if (ArrayUtility::isNullOrEmpty($parameters)) {
			if (is_string($message)) {
				return $message;
			}

			return var_export($message, true);
		}
		return var_export(['message' => $message, 'parameters' => $parameters], true);
	}

	public static function format(int $level, int $traceIndex, string $header, $message, ...$parameters): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		//DEBUG_BACKTRACE_PROVIDE_OBJECT
		/** @var array[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		/** @var array<string,mixed> */
		$traceCaller = $backtrace[$traceIndex];
		/** @var array<string,mixed> */
		$traceMethod = $backtrace[$traceIndex + 1];

		$map = [
			'TIMESTAMP' => date('c'),
			'IP' => ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', ''),
			'REQUEST_ID' => LOG_REQUEST_ID,
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
			'LEVEL' => self::formatLevel($level),
			'HEADER' => $header,
			'MESSAGE' => self::formatMessage($message, ...$parameters),
		];

		return StringUtility::replaceMap(self::$format, $map);
	}

	public static function create(string $header, int $baseTraceIndex = 0): ILogger
	{
		self::$initializeChecker->throwIfNotInitialize();

		$loggers = [
			new FileLogger($header, self::$level, $baseTraceIndex + 1, self::$loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$level, $baseTraceIndex, $loggers);
	}
}
