<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Mvc\Middleware\IMiddleware;

class RouteSetting
{
	/**
	 * Undocumented variable
	 *
	 * @var array<IMiddleware|string>
	 */
	public array $globalMiddleware;
	/**
	 * Undocumented variable
	 *
	 * @var array<IMiddleware|string>
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
	 * @param array<IMiddleware|string> $globalMiddleware
	 * @param array<IMiddleware|string> $actionMiddleware
	 * @param Route[] $routes
	 */
	public function __construct(array $globalMiddleware, array $actionMiddleware, array $routes)
	{
		$this->globalMiddleware = $globalMiddleware;
		$this->actionMiddleware = $actionMiddleware;
		$this->routes = $routes;
	}
}
