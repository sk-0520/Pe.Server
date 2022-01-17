<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * ルーティングの設定。
 */
class RouteSetting
{
	/**
	 * 全体適用ミドルウェア。
	 *
	 * @var array<IMiddleware|string>
	 */
	public array $globalMiddleware;
	/**
	 * アクション適用ミドルウェア。
	 *
	 * @var array<IMiddleware|string>
	 */
	public array $actionMiddleware;
	/**
	 * 全体適用応答完了後ミドルウェア。
	 *
	 * @var array<IShutdownMiddleware|string>
	 */
	public array $globalShutdownMiddleware;
	/**
	 * アクション適用応答完了後ミドルウェア。
	 *
	 * @var array<IShutdownMiddleware|string>
	 */
	public array $actionShutdownMiddleware;
	/**
	 * ルーティング一覧。
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
