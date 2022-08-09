<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\DefaultValue;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * HTTPメソッドとコントローラメソッドの紐づけ。
 *
 * @immutable
 */
class ActionSetting
{
	/**
	 * 生成。
	 *
	 * @param string $controllerMethod コントローラの呼び出しメソッド名。
	 * @param array<IMiddleware|string> $actionMiddleware アクションミドルウェア。
	 * @phpstan-param array<IMiddleware|class-string<IMiddleware>> $actionMiddleware アクションミドルウェア。
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware シャットダウンミドルウェア。
	 * @phpstan-param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $shutdownMiddleware シャットダウンミドルウェア。
	 */
	public function __construct(
		public string $controllerMethod,
		public array $actionMiddleware,
		public array $shutdownMiddleware
	) {
	}

	public static function none(): ActionSetting
	{
		return new ActionSetting(DefaultValue::EMPTY_STRING, [], []);
	}
}
