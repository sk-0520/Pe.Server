<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
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
 * コンソールロガー。
 *
 * CLI モードで使用する想定。
 *
 * @codeCoverageIgnore
 */
class ConsoleLogger extends LoggerBase
{
	#region define

	public const FORMAT = "{TIMESTAMP} |{LEVEL}| {REQUEST_ID} {FILE}({LINE}) {FUNCTION} -> {MESSAGE}" ;

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

	#region function

	#endregion

	#region LoggerBase

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);
		echo $logMessage, PHP_EOL;
	}

	#endregion
}
