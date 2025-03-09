<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Time;

/**
 * 要求から応答までの全体に対する測定処理。
 */
final class PerformanceShutdownMiddleware implements IShutdownMiddleware
{
	public function __construct(
		private ILogger $logger
	) {
	}

	#region IShutdownMiddleware

	public function handleShutdown(MiddlewareArgument $argument): void
	{
		$time = \microtime(true) - (float)$argument->stores->special->getServer('REQUEST_TIME_FLOAT', 0.0);
		$this->logger->info('shutdown {0} sec', $time);
	}

	#endregion
}
