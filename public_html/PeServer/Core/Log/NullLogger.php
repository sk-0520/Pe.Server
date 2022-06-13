<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;

/**
 * なんも出力しないロガー。
 */
final class NullLogger extends LoggerBase
{
	/**
	 * 生成。
	 */
	public function __construct()
	{
	}

	protected final function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		//NONE
	}
}
