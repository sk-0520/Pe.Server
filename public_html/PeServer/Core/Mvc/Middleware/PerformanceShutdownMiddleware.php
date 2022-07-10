<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * 要求から応答までの全体に対する測定処理。
 * @SuppressWarnings(PHPMD.Superglobals)
 */
final class PerformanceShutdownMiddleware implements IShutdownMiddleware
{
	public function handleShutdown(MiddlewareArgument $argument): void
	{
		$time = microtime(true) - $argument->stores->special->getServer('REQUEST_TIME_FLOAT', 0.0);
		$argument->logger->info('{0} ms', $time);
	}
}
