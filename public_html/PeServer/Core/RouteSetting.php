<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

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
	 * @var array<IShutdownMiddleware|string>
	 */
	public array $globalShutdownMiddleware;
	/**
	 * Undocumented variable
	 *
	 * @var array<IShutdownMiddleware|string>
	 */
	public array $actionShutdownMiddleware;
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
	 * @param array<IShutdownMiddleware|string> $globalShutdownMiddleware
	 * @param array<IShutdownMiddleware|string> $actionShutdownMiddleware
	 * @param Route[] $routes
	 */
	public function __construct(array $globalMiddleware, array $actionMiddleware, array $globalShutdownMiddleware, array $actionShutdownMiddleware, array $routes)
	{
		$this->globalMiddleware = $globalMiddleware;
		$this->actionMiddleware = $actionMiddleware;
		$this->globalShutdownMiddleware = $globalShutdownMiddleware;
		$this->actionShutdownMiddleware = $actionShutdownMiddleware;
		$this->routes = $routes;
	}
}
