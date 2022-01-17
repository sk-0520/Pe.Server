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
	public HttpStatus $status;
	public string $className;
	public string $classMethod;
	/**
	 * パラメータ。
	 *
	 * @var array<string,string>
	 */
	public array $params;
	/**
	 * ミドルウェア一覧。
	 *
	 * @var array<IMiddleware|string>
	 */
	public array $middleware;
	/**
	 * Undocumented variable
	 *
	 * @var array<IShutdownMiddleware|string>
	 */
	public array $shutdownMiddleware;

	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param string $className
	 * @param string $classMethod
	 * @param array<string,string> $params
	 * @param array<IMiddleware|string> $middleware
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware
	 */
	public function __construct(HttpStatus $status, string $className, string $classMethod, array $params, array $middleware, array $shutdownMiddleware)
	{
		$this->status = $status;
		$this->className = $className;
		$this->classMethod = $classMethod;
		$this->params = $params;
		$this->middleware = $middleware;
		$this->shutdownMiddleware = $shutdownMiddleware;
	}
}
