<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;

/**
 * ミドルウェア。
 */
interface IMiddleware
{
	/**
	 * 前処理。
	 *
	 * @param MiddlewareArgument $argument
	 * @return MiddlewareResult
	 */
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult;

	/**
	 * 後処理。
	 *
	 * @param MiddlewareArgument $argument
	 * @return MiddlewareResult
	 */
	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult;
}
