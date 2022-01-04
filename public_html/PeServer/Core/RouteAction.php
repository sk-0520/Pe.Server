<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\HttpStatus;
use PeServer\Core\IMiddleware;


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
	 * @var IMiddleware[]
	 */
	public array $middleware;

	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param string $className
	 * @param string $classMethod
	 * @param array<string,string> $params
	 * @param IMiddleware[] $middleware
	 */
	public function __construct(HttpStatus $status, string $className, string $classMethod, array $params, array $middleware)
	{
		$this->status = $status;
		$this->className = $className;
		$this->classMethod = $classMethod;
		$this->params = $params;
		$this->middleware = $middleware;
	}
}
