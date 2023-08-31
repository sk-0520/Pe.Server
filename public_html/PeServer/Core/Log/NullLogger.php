<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;

/**
 * なんも出力しないロガー。
 */
final class NullLogger implements ILogger
{
	#region ILogger

	public function log(int $level, int $traceIndex, $message, ...$parameters): void
	{
		//NOP
	}
	public function trace($message, ...$parameters): void
	{
		//NOP
	}
	public function debug($message, ...$parameters): void
	{
		//NOP
	}
	public function info($message, ...$parameters): void
	{
		//NOP
	}
	public function warn($message, ...$parameters): void
	{
		//NOP
	}
	public function error($message, ...$parameters): void
	{
		//NOP
	}

	#endregion
}
