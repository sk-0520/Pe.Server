<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

class PerformanceShutdownMiddleware implements IShutdownMiddleware
{
	public function handleShutdown(MiddlewareArgument $argument): void
	{
		$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
		$argument->logger->info('{0} ms', $time);
	}
}
