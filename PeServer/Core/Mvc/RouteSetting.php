<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * ルーティングの設定。
 */
readonly class RouteSetting
{
	/**
	 * 生成。
	 *
	 * @param array<IMiddleware|class-string<IMiddleware>> $globalMiddleware 全体適用ミドルウェア。
	 * @param array<IMiddleware|class-string<IMiddleware>> $actionMiddleware アクション適用ミドルウェア。
	 * @param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $globalShutdownMiddleware 全体適用応答完了後ミドルウェア。
	 * @param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $actionShutdownMiddleware アクション適用応答完了後ミドルウェア。
	 * @param RouteInformation[] $routes ルーティング一覧。
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
