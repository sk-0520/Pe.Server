<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

/**
 * ミドルウェア。
 */
interface IMiddleware
{
	/**
	 * 前処理。
	 *
	 * @param MiddlewareArgument $argument ミドルウェアの入力パラメータ。
	 * @return MiddlewareResult ミドルウェア結果。
	 */
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult;

	/**
	 * 後処理。
	 *
	 * @param MiddlewareArgument $argument ミドルウェアの入力パラメータ。
	 * @param HttpResponse $response
	 * @return MiddlewareResult ミドルウェア結果。
	 */
	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult;
}
