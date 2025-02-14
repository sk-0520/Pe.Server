<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Code;
use PeServer\Core\Collection\Arr;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\LogOptions;
use PeServer\Core\Text;
use PeServer\Core\Throws\Throws;

/**
 * RAMため込み。
 *
 * @codeCoverageIgnore
 */
final class StaticRamLogger extends LoggerBase
{
	#region define

	public const FORMAT = "{TIMESTAMP} |{LEVEL}| {FILE}({LINE}) {FUNCTION} -> {MESSAGE}";

	#endregion

	#region variable

	/** @var string[] */
	public static array $logs = [];

	#endregion

	/**
	 * 生成。
	 *
	 * @param LogOptions $options
	 */
	public function __construct(Logging $logging, LogOptions $options)
	{
		parent::__construct($logging, $options);
	}

	#region LoggerBase

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		self::$logs[] = $logMessage;
	}

	#endregion
}
