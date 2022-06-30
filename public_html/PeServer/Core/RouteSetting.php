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
	 * 生成。
	 *
	 * @param array<IMiddleware|string> $globalMiddleware 全体適用ミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $globalMiddleware 全体適用ミドルウェア。
	 * @param array<IMiddleware|string> $actionMiddleware アクション適用ミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $actionMiddleware アクション適用ミドルウェア。
	 * @param array<IShutdownMiddleware|string> $globalShutdownMiddleware 全体適用応答完了後ミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $globalShutdownMiddleware 全体適用応答完了後ミドルウェア。
	 * @param array<IShutdownMiddleware|string> $actionShutdownMiddleware アクション適用応答完了後ミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $actionShutdownMiddleware アクション適用応答完了後ミドルウェア。
	 * @param Route[] $routes ルーティング一覧。
	 */
	public function __construct(
		/** @readonly */
		public array $globalMiddleware,
		/** @readonly */
		public array $actionMiddleware,
		/** @readonly */
		public array $globalShutdownMiddleware,
		/** @readonly */
		public array $actionShutdownMiddleware,
		/** @readonly */
		public array $routes
	) {}
}
