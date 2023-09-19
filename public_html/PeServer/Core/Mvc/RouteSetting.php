<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * ルーティングの設定。
 *
 * @immutable
 */
class RouteSetting
{
	/**
	 * 生成。
	 *
	 * @param array<IMiddleware|string> $globalMiddleware 全体適用ミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $globalMiddleware
	 * @param array<IMiddleware|string> $actionMiddleware アクション適用ミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $actionMiddleware
	 * @param array<IShutdownMiddleware|string> $globalShutdownMiddleware 全体適用応答完了後ミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $globalShutdownMiddleware
	 * @param array<IShutdownMiddleware|string> $actionShutdownMiddleware アクション適用応答完了後ミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $actionShutdownMiddleware
	 * @param Route[] $routes ルーティング一覧。
	 */
	public function __construct(
		public array $globalMiddleware,
		public array $actionMiddleware,
		public array $globalShutdownMiddleware,
		public array $actionShutdownMiddleware,
		public array $routes
	) {
    }
}
