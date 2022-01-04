<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\MiddlewareResult;
use PeServer\Core\MiddlewareArgument;

/**
 * ミドルウェア。
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
