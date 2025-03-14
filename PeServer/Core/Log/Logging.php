<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use DateTimeImmutable;
use DateTimeInterface;
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
 * ロガー生成・共通処理。
 *
 * @phpstan-import-type MessageAlias from ILogger
 */
class Logging
{
	#region define

	private const LOG_REQUEST_ID_LENGTH = 6;
	private const IS_ENABLED_HOST = true;

	#endregion

	#region variable

	private string $requestId;
	private ?string $requestHost = null;

	private SpecialStore $specialStore;

	#endregion

	#region function

	public function __construct(SpecialStore $specialStore)
	{
		$this->requestId = Cryptography::generateRandomBinary(self::LOG_REQUEST_ID_LENGTH)->toHex();
		$this->specialStore = $specialStore;
	}

	/**
	 * ログレベル書式化。
	 *
	 * @param int $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level
	 * @return string
	 * @phpstan-pure
	 */
	private static function formatLevel(int $level): string
	{
		return match ($level) {
			ILogger::LOG_LEVEL_TRACE => 'TRACE',
			ILogger::LOG_LEVEL_DEBUG => 'DEBUG',
			ILogger::LOG_LEVEL_INFORMATION => 'INFO ',
			ILogger::LOG_LEVEL_WARNING => 'WARN ',
			ILogger::LOG_LEVEL_ERROR => 'ERROR',
		};
	}

	/**
	 * メッセージ書式適用。
	 *
	 * @param mixed $message
	 * @phpstan-param MessageAlias $message
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

	private function getRemoteHost(): string
	{
		// @phpstan-ignore-next-line [DEBUG]
		if (!self::IS_ENABLED_HOST) {
			return Text::EMPTY;
		}

		if ($this->requestHost !== null) {
			return $this->requestHost;
		}

		/** @var string */
		$serverRemoteHost = $this->specialStore->getServer('REMOTE_HOST', Text::EMPTY);
		if ($serverRemoteHost !== Text::EMPTY) {
			return $this->requestHost = $serverRemoteHost;
		}

		/** @var string */
		$serverRemoteIpAddr = $this->specialStore->getServer('REMOTE_ADDR', Text::EMPTY);
		if ($serverRemoteIpAddr === Text::EMPTY) {
			return $this->requestHost = Text::EMPTY;
		}

		$hostName = gethostbyaddr($serverRemoteIpAddr);
		if ($hostName === false) {
			return $this->requestHost = Text::EMPTY;
		}

		return $this->requestHost = $hostName;
	}

	/**
	 * ログで使う共通的なやつら
	 *
	 * @param DateTimeInterface $timestamp
	 * @param SpecialStore $specialStore
	 * @return array{TIMESTAMP:string,DATE:string,TIME:string,TIMEZONE:string,CLIENT_IP:string,CLIENT_HOST:string,REQUEST_ID:string,UA:string,METHOD:string,REQUEST:string,SESSION:string|false}
	 */
	public function getLogParameters(DateTimeInterface $timestamp, SpecialStore $specialStore): array
	{
		return [
			'TIMESTAMP' => $timestamp->format('c'),
			'DATE' => $timestamp->format('Y-m-d'),
			'TIME' => $timestamp->format('H:i:s'),
			'TIMEZONE' => $timestamp->format('P'),
			'CLIENT_IP' => $specialStore->getServer('REMOTE_ADDR', Text::EMPTY),
			'CLIENT_HOST' => self::getRemoteHost(),
			'REQUEST_ID' => $this->requestId,
			'UA' => $specialStore->getServer('HTTP_USER_AGENT', Text::EMPTY),
			'METHOD' => $specialStore->getServer('REQUEST_METHOD', Text::EMPTY),
			'REQUEST' => $specialStore->getServer('REQUEST_URI', Text::EMPTY),
			'SESSION' => session_id(),
			'REFERER' => $specialStore->getServer('HTTP_REFERER', Text::EMPTY),
		];
	}

	/**
	 * ログ書式適用。
	 *
	 * @param string $format
	 * @phpstan-param literal-string $format
	 * @param int $level
	 * @phpstan-param ILogger::LOG_LEVEL_* $level 有効レベル。S
	 * @param int $level
	 * @param int $traceIndex
	 * @phpstan-param non-negative-int $traceIndex
	 * @param non-empty-string $header
	 * @param mixed $message
	 * @phpstan-param MessageAlias $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public function format(string $format, int $level, int $traceIndex, DateTimeInterface $timestamp, string $header, $message, ...$parameters): string
	{
		/** @var array<string,array<string,mixed>>[] */
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS); // DEBUG_BACKTRACE_PROVIDE_OBJECT
		$traceCaller = $backtrace[$traceIndex];
		$traceMethod = $backtrace[$traceIndex + 1];

		/** @var string */
		$filePath = $traceCaller['file'] ?? Text::EMPTY;

		/** @var array<string,string> */
		$map = [
			...self::getLogParameters($timestamp, $this->specialStore),
			//-------------------
			'FILE' => $filePath,
			'FILE_NAME' => Path::getFileName($filePath),
			'LINE' => $traceCaller['line'] ?? 0,
			//'CLASS' => $traceMethod['class'] ?? Text::EMPTY,
			'FUNCTION' => $traceMethod['function'] ?? Text::EMPTY,
			//'ARGS' => $traceMethod['args'] ?? Text::EMPTY,
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
	 * @return non-empty-string
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
				return $name;
			}
		}

		if (Text::isNullOrEmpty($header)) {
			return TypeUtility::getType($input);
		}

		return $header;
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
