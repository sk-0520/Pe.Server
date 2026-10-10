<?php

declare(strict_types=1);

namespace PeServer\Core\Middleware;

/**
 * ミドルウェア
 *
 * @template TMiddlewareInput of IMiddlewareInput
 * @template TMiddlewareOutput of IMiddlewareOutput
 */
interface IMiddleware
{
	/**
	 * ミドルウェア処理。
	 *
	 * @param TMiddlewareInput $input
	 * @phpstan-param TMiddlewareInput $input
	 * @param IHandler $next
	 * @phpstan-param IHandler<TMiddlewareInput, TMiddlewareOutput> $next
	 * @return TMiddlewareOutput
	 * @phpstan-return TMiddlewareOutput
	 */
	public function process(IMiddlewareInput $input, IHandler $next): IMiddlewareOutput;
}
