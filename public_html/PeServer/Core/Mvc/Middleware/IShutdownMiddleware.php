<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareArgument;

/**
 * 応答完了後ミドルウェア。
 */
interface IShutdownMiddleware
{
	/**
	 * 応答完了処理。
	 *
	 * @param MiddlewareArgument $argument ミドルウェアの入力パラメータ。
	 * @return void
	 */
	public function handleShutdown(MiddlewareArgument $argument): void;
}
