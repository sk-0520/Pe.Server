<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \DateTimeImmutable;
use PeServer\Core\Log\ILogger;
use PeServer\Core\FileUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;
use PeServer\Core\Log\FileLogger;
use PeServer\Core\Log\MultiLogger;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Throws\NotImplementedException;

/**
 * ロガー生成処理。
 *
 * DIコンテナとか無いのでこいつを静的に使用してログがんばる。
 */
abstract class Logging
{
	private const LOG_REQUEST_ID_LENGTH = 6;
	private const IS_ENABLED_HOST = true;

	static string $requestId;
	static ?string $requestHost = null;

	/**
	 * 初期化チェック。
	 *
	 * @var InitializeChecker
	 */
	private static InitializeChecker $initializeChecker;

	/**
	 * ログ設定。
	 *
	 * @var array<string,mixed>
	 */
	private static array $loggingConfiguration;

	/**
	 * ログレベル。
	 *
	 * @var int
	 * @phpstan-var ILogger::LEVEL_*
	 */
	private static int $level;

	//public static string $defaultFormat = '{TIMESTAMP} |{LEVEL}| [{CLIENT_IP}:{CLIENT_HOST}] {REQUEST_ID}|{SESSION} <{UA}> {METHOD} {REQUEST} {FILE}({LINE}) {FUNCTION} -> {MESSAGE}';

	/**
	 * 初期化。
	 *
	 * @param array<string,mixed> $loggingConfiguration
	 * @return void
	 */
	public static function initialize(array $loggingConfiguration)
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$loggingConfiguration = $loggingConfiguration;
		self::$requestId = Cryptography::generateRandomBytes(self::LOG_REQUEST_ID_LENGTH)->toHex();

		/**
		 * @var int
		 * @phpstan-var ILogger::LEVEL_*
		 *
		 */
		$level = ArrayUtility::getOr(self::$loggingConfiguration, 'level', ILogger::LEVEL_INFORMATION);

		self::$level = $level;
	}

	private static function formatLevel(int $level): string
	{
		return match ($level) {
			ILogger::LEVEL_TRACE => 'TRACE',
			ILogger::LEVEL_DEBUG => 'DEBUG',
			ILogger::LEVEL_INFORMATION => 'INFO ',
			ILogger::LEVEL_WARNING => 'WARN ',
			ILogger::LEVEL_ERROR => 'ERROR',
			default => throw new NotImplementedException(),
		};
	}

	/**
	 * メッセージ書式適用。
	 *
	 * @param mixed $message
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters
	 * @return string
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	private static function formatMessage($message, ...$parameters): string
	{
		if (is_null($message)) {
			if (ArrayUtility::isNullOrEmpty($parameters)) {
				return InitialValue::EMPTY_STRING;
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

	private static function getRemoteHost(): string
	{
		// @phpstan-ignore-next-line
		if (!self::IS_ENABLED_HOST) {
			return InitialValue::EMPTY_STRING;
		}

		if (self::$requestHost !== null) {
			return self::$requestHost;
		}

		/** @var string */
		$serverRemoteHost = ArrayUtility::getOr($_SERVER, 'REMOTE_HOST', InitialValue::EMPTY_STRING);
		if ($serverRemoteHost !== InitialValue::EMPTY_STRING) {
			return self::$requestHost = $serverRemoteHost;
		}

		/** @var string */
		$serverRemoteIpAddr = ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', InitialValue::EMPTY_STRING);
		if ($serverRemoteIpAddr === InitialValue::EMPTY_STRING) {
			return self::$requestHost = InitialValue::EMPTY_STRING;
		}

		/** @var string|false */
		$hostName = gethostbyaddr($serverRemoteIpAddr);
		if ($hostName === false) {
			return self::$requestHost = InitialValue::EMPTY_STRING;
		}

		return self::$requestHost = $hostName;
	}

	/**
	 * ログ書式適用。
	 *
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param integer $level
	 * @phpstan-param ILogger::LEVEL_* $level 有効レベル。S
	 * @param integer $level
	 * @param integer $traceIndex
	 * @param string $header
	 * @param mixed $message
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public static function format(string $format, int $level, int $traceIndex, string $header, $message, ...$parameters): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		/** @var array<string,mixed>[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // DEBUG_BACKTRACE_PROVIDE_OBJECT
		/** @var array<string,mixed> */
		$traceCaller = $backtrace[$traceIndex];
		/** @var array<string,mixed> */
		$traceMethod = $backtrace[$traceIndex + 1];

		$timestamp = new DateTimeImmutable();
		/** @var string */
		$filePath = ArrayUtility::getOr($traceCaller, 'file', InitialValue::EMPTY_STRING);

		/** @var array<string,string> */
		$map = [
			'TIMESTAMP' => $timestamp->format('c'),
			'DATE' => $timestamp->format('Y-m-d'),
			'TIME' => $timestamp->format('H:i:s'),
			'TIMEZONE' => $timestamp->format('P'),
			'CLIENT_IP' => ArrayUtility::getOr($_SERVER, 'REMOTE_ADDR', InitialValue::EMPTY_STRING),
			'CLIENT_HOST' => self::getRemoteHost(),
			'REQUEST_ID' => self::$requestId,
			'UA' => ArrayUtility::getOr($_SERVER, 'HTTP_USER_AGENT', InitialValue::EMPTY_STRING),
			'METHOD' => ArrayUtility::getOr($_SERVER, 'REQUEST_METHOD', InitialValue::EMPTY_STRING),
			'REQUEST' => ArrayUtility::getOr($_SERVER, 'REQUEST_URI', InitialValue::EMPTY_STRING),
			'SESSION' => session_id(),
			//-------------------
			'FILE' => $filePath,
			'FILE_NAME' => PathUtility::getFileName($filePath),
			'LINE' => ArrayUtility::getOr($traceCaller, 'line', 0),
			//'CLASS' => ArrayUtility::getOr($traceMethod, 'class', InitialValue::EMPTY_STRING),
			'FUNCTION' => ArrayUtility::getOr($traceMethod, 'function', InitialValue::EMPTY_STRING),
			//'ARGS' => ArrayUtility::getOr($traceMethod, 'args', InitialValue::EMPTY_STRING),
			//-------------------
			'LEVEL' => self::formatLevel($level),
			'HEADER' => $header,
			'MESSAGE' => self::formatMessage($message, ...$parameters),
		];

		return StringUtility::replaceMap($format, $map);
	}

	public static function create(string $header, int $baseTraceIndex = 0): ILogger
	{
		self::$initializeChecker->throwIfNotInitialize();

		$loggers = [
			//@phpstan-ignore-next-line
			new FileLogger(ArrayUtility::getOr(self::$loggingConfiguration, 'format', ''), $header, self::$level, $baseTraceIndex + 1,
			/** @var array<mixed> */
			self::$loggingConfiguration['file']),
		];
		if (function_exists('xdebug_is_debugger_active') && \xdebug_is_debugger_active()) {
			$loggers[] = new XdebugLogger($header, self::$level, $baseTraceIndex + 1);
		}
		return new MultiLogger($header, self::$level, $baseTraceIndex, $loggers);
	}
}
