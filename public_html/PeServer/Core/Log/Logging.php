<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

/** @var int */
const LOG_REQUEST_ID_LENGTH = 6;
define('LOG_REQUEST_ID', bin2hex(openssl_random_pseudo_bytes(LOG_REQUEST_ID_LENGTH)));

use \LogicException;
use PeServer\Core\ArrayUtility;
use PeServer\Core\ILogger;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Log\FileLogger;
use PeServer\Core\Log\MultiLogger;
use PeServer\Core\StringUtility;

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
	private static $initializeChecker;

	/**
	 * ログ設定。
	 *
	 * @var array<string,mixed>
	 */
	private static $loggingConfiguration;

	/**
	 * ログレベル。
	 *
	 * @var int
	 */
	private static $level;
	/**
	 * 書式設定。
	 *
	 * @var string
	 */
	private static $format;

	/**
	 * Undocumented function
	 *
	 * @param array<string,mixed> $loggingConfiguration
	 * @return void
	 */
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
			return StringUtility::dump($parameters);
		}

		if (is_string($message) && !ArrayUtility::isNullOrEmpty($parameters) && array_keys($parameters)[0] === 0) {
			/** @var string[] */
			$values = array_map(function ($value) {
				if (is_string($value)) {
					return $value;
				}
				if (is_object($value) || is_array($value)) {
					return StringUtility::dump($value);
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

			return StringUtility::dump($message);
		}
		return StringUtility::dump(['message' => $message, 'parameters' => $parameters]);
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
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		/** @var array<string,mixed>[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // DEBUG_BACKTRACE_PROVIDE_OBJECT
		/** @var array<string,mixed> */
		$traceCaller = $backtrace[$traceIndex];
		/** @var array<string,mixed> */
		$traceMethod = $backtrace[$traceIndex + 1];

		/** @var array<string,string> */
		$map = [
			'TIMESTAMP' => date('c'),
			'CLIENT_IP' => ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', ''),
			'CLIENT_HOST' => ArrayUtility::getOr($_SERVER, 'REMOTE_HOST', ''),
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
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$loggers = [
			new FileLogger($header, self::$level, $baseTraceIndex + 1, self::$loggingConfiguration['file']),
		];
		return new MultiLogger($header, self::$level, $baseTraceIndex, $loggers);
	}
}
