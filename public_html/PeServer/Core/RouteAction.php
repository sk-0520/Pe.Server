<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;


/**
 * ルーティングのアクション設定。
 */
class RouteAction
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param string $className
	 * @param string $classMethod
	 * @param array<string,string> $params パラメータ。
	 * @param array<IMiddleware|string> $middleware ミドルウェア一覧。
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware
	 */
	public function __construct(
		public HttpStatus $status,
		public string $className,
		public string $classMethod,
		public array $params,
		public array $middleware,
		public array $shutdownMiddleware
	) {
	}
}
