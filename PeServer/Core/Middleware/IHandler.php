<?php

declare(strict_types=1);

namespace PeServer\Core\Middleware;

/**
 * ハンドラー。
 *
 * @template TMiddlewareInput of IMiddlewareInput
 * @template TMiddlewareOutput of IMiddlewareOutput
 */
interface IHandler
{
	/**
	 * ハンドラー処理。
	 *
	 * @param TMiddlewareInput $input
	 * @phpstan-param TMiddlewareInput $input
	 * @return TMiddlewareOutput
	 * @phpstan-return TMiddlewareOutput
	 */
	public function handle(IMiddlewareInput $input): IMiddlewareOutput;
}
