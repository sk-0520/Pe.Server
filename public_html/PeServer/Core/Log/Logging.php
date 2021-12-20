<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

// @phpstan-ignore-next-line
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
abstract class Logging
{
	/**
	 * 初期化チェック。
	 *
	 * @var InitializeChecker|null
	 */
	private static $_initializeChecker;

	/**
	 * ログ設定。
	 *
	 * @var array
	 */
	private static $_loggingConfiguration; // @phpstan-ignore-line

	/**
	 * ログレベル。
	 *
	 * @var int
	 */
	private static $_level;
	/**
	 * 書式設定。
	 *
	 * @var string
	 */
	private static $_format;

	public static function initialize(array $loggingConfiguration) // @phpstan-ignore-line
	{
		if (is_null(self::$_initializeChecker)) {
			self::$_initializeChecker = new InitializeChecker();
		}
		self::$_initializeChecker->initialize();

		self::$_loggingConfiguration = $loggingConfiguration;

		self::$_level = self::$_loggingConfiguration['level'];
		self::$_format = self::$_loggingConfiguration['format'];
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

	/**
	 * メッセージ書式適用。
	 *
	 * @param mixed $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	private static function formatMessage($message, ...$parameters): string
	{
		if (is_null($message)) {
			if (ArrayUtility::isNullOrEmpty($parameters)) {
				return '';
			}
			return var_export($parameters, true);
		}

		if (is_string($message) && !ArrayUtility::isNullOrEmpty($parameters) && array_keys($parameters)[0] === 0) {
			/** @var string[] */
			$values = array_map(function ($value) {
				if (is_string($value)) {
					return $value;
				}
				if (is_object($value) || is_array($value)) {
					return var_export($value, true);
				}

				return strval($value);
			}, $parameters);

			/** @var array<string,string> */
			$map = array();
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

	/**
	 * ログ書式適用。
	 *
	 * @param integer $level
	 * @param integer $traceIndex
	 * @param string $header
	 * @param mixed $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public static function format(int $level, int $traceIndex, string $header, $message, ...$parameters): string
	{
		self::$_initializeChecker->throwIfNotInitialize();

		//DEBUG_BACKTRACE_PROVIDE_OBJECT
		/** @var array[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // @phpstan-ignore-line
		/** @var array<string,mixed> */
		$traceCaller = $backtrace[$traceIndex];
		/** @var array<string,mixed> */
		$traceMethod = $backtrace[$traceIndex + 1];

		/** @var array<string,string> */
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

		return StringUtility::replaceMap(self::$_format, $map);
	}

	public static function create(string $header, int $baseTraceIndex = 0): ILogger
	{
		self::$_initializeChecker->throwIfNotInitialize();

		$loggers = [
			new FileLogger($header, self::$_level, $baseTraceIndex + 1, self::$_loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$_level, $baseTraceIndex, $loggers);
	}
}
