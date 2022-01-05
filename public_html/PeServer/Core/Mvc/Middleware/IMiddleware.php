<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;

/**
 * ミドルウェア。
 *
 * 入力しか受け付けませーん。
 */
interface IMiddleware
{
	/**
	 * 処理内容。
	 *
	 * callable で書くのがしんどいんよ。
	 *
	 * @param MiddlewareArgument $argument
	 * @return MiddlewareResult
	 */
	public function handle(MiddlewareArgument $argument): MiddlewareResult;
}
