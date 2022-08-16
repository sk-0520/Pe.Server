<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Log\ILogger;

interface ILoggerFactory
{
	/**
	 * ロガー生成。
	 *
	 * @param string|object $header
	 * @phpstan-param non-empty-string|object $header
	 * @param int $baseTraceIndex
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @return ILogger
	 */
	function create(string|object $header, int $baseTraceIndex = 0): ILogger;
}
