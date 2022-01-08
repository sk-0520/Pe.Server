<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;

/**
 * 応答完了後ミドルウェア。
 */
interface IShutdownMiddleware
{
	public function handleShutdown(MiddlewareArgument $argument): void;
}
