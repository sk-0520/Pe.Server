<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \DateTimeImmutable;
use \DateTimeInterface;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Cryptography;
use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\InitializeChecker;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\TypeUtility;

/**
 * ロガー生成処理。
 *
 * DIコンテナとか無いのでこいつを静的に使用してログがんばる。
 */
abstract class Logging
{
	#region define

	private const LOG_REQUEST_ID_LENGTH = 6;
	private const IS_ENABLED_HOST = true;

	#endregion

	#region variable

	static string $requestId;
	static ?string $requestHost = null;

	/**
	 * 初期化チェック。
	 *
	 * @var InitializeChecker
	 */
	private static InitializeChecker $initializeChecker;

	private static SpecialStore $specialStore;

	#endregion

	#region function

	/**
	 * 初期化。
	 *
	 * @param SpecialStore $specialStore
	 * @return void
	 */
	public static function initialize(SpecialStore $specialStore)
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$requestId = Cryptography::generateRandomBinary(self::LOG_REQUEST_ID_LENGTH)->toHex();
		self::$specialStore = $specialStore;
	}

	private static function formatLevel(int $level): string
	{
		return match ($level) {
			ILogger::LOG_LEVEL_TRACE => 'TRACE',
			ILogger::LOG_LEVEL_DEBUG => 'DEBUG',
			ILogger::LOG_LEVEL_INFORMATION => 'INFO ',
			ILogger::LOG_LEVEL_WARNING => 'WARN ',
			ILogger::LOG_LEVEL_ERROR => 'ERROR',
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
	 */
	private static function formatMessage($message, ...$parameters): string
	{
		if ($message === null) {
			if (Arr::isNullOrEmpty($parameters)) {
				return Text::EMPTY;
			}
			return Text::dump($parameters);
		}

		if (is_string($message) && !Arr::isNullOrEmpty($parameters) && array_keys($parameters)[0] === 0) {
			/** @var string[] */
			$values = array_map(function ($value) {
				if (is_string($value)) {
					return $value;
				}
				if (is_object($value) || is_array($value)) {
					return Text::dump($value);
				}

				return strval($value);
			}, $parameters);

			/** @var array<string,string> */
			$map = [];
			foreach ($values as $key => $value) {
				$map[strval($key)] = $value;
			}

			return Text::replaceMap($message, $map);
		}

		if (Arr::isNullOrEmpty($parameters)) {
			if (is_string($message)) {
				return $message;
			}

			return Text::dump($message);
		}
		return Text::dump(['message' => $message, 'parameters' => $parameters]);
	}

	private static function getRemoteHost(): string
	{
		// @phpstan-ignore-next-line
		if (!self::IS_ENABLED_HOST) {
			return Text::EMPTY;
		}

		if (self::$requestHost !== null) {
			return self::$requestHost;
		}

		/** @var string */
		$serverRemoteHost = self::$specialStore->getServer('REMOTE_HOST', Text::EMPTY);
		if ($serverRemoteHost !== Text::EMPTY) {
			return self::$requestHost = $serverRemoteHost;
		}

		/** @var string */
		$serverRemoteIpAddr = self::$specialStore->getServer('REMOTE_ADDR', Text::EMPTY);
		if ($serverRemoteIpAddr === Text::EMPTY) {
			return self::$requestHost = Text::EMPTY;
		}

		/** @var string|false */
		$hostName = gethostbyaddr($serverRemoteIpAddr);
		if ($hostName === false) {
			return self::$requestHost = Text::EMPTY;
		}

		return self::$requestHost = $hostName;
	}

	/**
	 * ログ書式適用。
	 *
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param integer $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level 有効レベル。S
	 * @param integer $level
	 * @param integer $traceIndex
	 * @phpstan-param UnsignedIntegerAlias $traceIndex
	 * @param string $header
	 * @phpstan-param non-empty-string $header
	 * @param mixed $message
	 * @phpstan-param LogMessageAlias $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public static function format(string $format, int $level, int $traceIndex, DateTimeInterface $timestamp, string $header, $message, ...$parameters): string
	{
		self::$initializeChecker->throwIfNotInitialize();

		/** @var array<string,mixed>[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // DEBUG_BACKTRACE_PROVIDE_OBJECT
		/** @var array<string,mixed> */
		$traceCaller = $backtrace[$traceIndex];
		/** @var array<string,mixed> */
		$traceMethod = $backtrace[$traceIndex + 1];

		/** @var string */
		$filePath = Arr::getOr($traceCaller, 'file', Text::EMPTY);

		/** @var array<string,string> */
		$map = [
			'TIMESTAMP' => $timestamp->format('c'),
			'DATE' => $timestamp->format('Y-m-d'),
			'TIME' => $timestamp->format('H:i:s'),
			'TIMEZONE' => $timestamp->format('P'),
			'CLIENT_IP' => self::$specialStore->getServer('REMOTE_ADDR', Text::EMPTY),
			'CLIENT_HOST' => self::getRemoteHost(),
			'REQUEST_ID' => self::$requestId,
			'UA' => self::$specialStore->getServer('HTTP_USER_AGENT', Text::EMPTY),
			'METHOD' => self::$specialStore->getServer('REQUEST_METHOD', Text::EMPTY),
			'REQUEST' => self::$specialStore->getServer('REQUEST_URI', Text::EMPTY),
			'SESSION' => session_id(),
			//-------------------
			'FILE' => $filePath,
			'FILE_NAME' => Path::getFileName($filePath),
			'LINE' => Arr::getOr($traceCaller, 'line', 0),
			//'CLASS' => Arr::getOr($traceMethod, 'class', Text::EMPTY),
			'FUNCTION' => Arr::getOr($traceMethod, 'function', Text::EMPTY),
			//'ARGS' => Arr::getOr($traceMethod, 'args', Text::EMPTY),
			//-------------------
			'LEVEL' => self::formatLevel($level),
			'HEADER' => $header,
			'MESSAGE' => self::formatMessage($message, ...$parameters),
		];

		return Text::replaceMap($format, $map);
	}

	/**
	 * ヘッダ名生成。
	 *
	 * @param string|object $input
	 * @return string
	 * @phpstan-return non-empty-string
	 */
	public static function toHeader(string|object $input): string
	{
		$header = is_string($input)
			? $input
			: TypeUtility::getType($input);

		if (Text::contains($header, '\\', false)) {
			$names = Text::split($header, '\\');
			$name = $names[count($names) - 1];
			if (!Text::isNullOrEmpty($name)) {
				return $name; //@phpstan-ignore-line !Text::isNullOrEmpty
			}
		}

		if (Text::isNullOrEmpty($header)) {
			return TypeUtility::getType($input);
		}

		return $header; //@phpstan-ignore-line !Text::isNullOrEmpty
	}

	/**
	 * ILoggerに対して注入処理。
	 *
	 * @param IDiContainer $container
	 * @param DiItem[] $callStack
	 * @return ILogger
	 */
	public static function injectILogger(IDiContainer $container, array $callStack): ILogger
	{
		$loggerFactory = $container->get(ILoggerFactory::class);
		if (/*1 < */count($callStack)) {
			//$item = $callStack[count($callStack) - 2];
			$item = $callStack[0];
			$className = (string)$item->data; // あぶねぇかなぁ
			$header = Logging::toHeader($className);
			return $loggerFactory->createLogger($header, 0);
		}
		return $loggerFactory->createLogger('<UNKNOWN>');
	}

	#endregion
}
