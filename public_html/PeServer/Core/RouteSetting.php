<?php

declare(strict_types=1);

namespace PeServer\Core;

class RouteSetting
{
	/**
	 * Undocumented variable
	 *
	 * @var IMiddleware[]
	 */
	public array $globalMiddleware;
	/**
	 * Undocumented variable
	 *
	 * @var IMiddleware[]
	 */
	public array $actionMiddleware;
	/**
	 * Undocumented variable
	 *
	 * @var Route[]
	 */
	public array $routes;

	/**
	 * 生成。
	 *
	 * @param IMiddleware[] $globalMiddleware
	 * @param IMiddleware[] $actionMiddleware
	 * @param Route[] $routes
	 */
	public function __construct(array $globalMiddleware, array $actionMiddleware, array $routes)
	{
		$this->globalMiddleware = $globalMiddleware;
		$this->actionMiddleware = $actionMiddleware;
		$this->routes = $routes;
	}
}
