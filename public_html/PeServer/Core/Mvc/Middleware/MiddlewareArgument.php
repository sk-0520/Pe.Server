<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Store\Stores;


/**
 * ミドルウェアの入力パラメータ。
 */
class MiddlewareArgument
{
	/**
	 * 生成。
	 *
	 * @param RequestPath $requestPath
	 * @param Stores $stores
	 * @param HttpRequest $request
	 */
	public function __construct(
		public RequestPath $requestPath,
		public Stores $stores,
		public HttpRequest $request
	) {
	}
}
