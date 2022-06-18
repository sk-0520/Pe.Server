<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * HTTPメソッドとコントローラメソッドの紐づけ。
 */
class ActionRelation
{
	public function __construct(
		/**
		 * コントローラの呼び出しメソッド名。
		 *
		 * @var string
		 */
		public string $controllerMethod,
		/**
		 * アクションミドルウェア。
		 *
		 * @var array<IMiddleware|string>
		 */
		public array $actionMiddleware,
		/**
		 * シャットダウンミドルウェア。
		 *
		 * @var array<IShutdownMiddleware|string>
		 */
		public array $shutdownMiddleware
	) {
	}

	public static function none(): ActionRelation
	{
		return new ActionRelation(InitialValue::EMPTY_STRING, [], []);
	}
}
