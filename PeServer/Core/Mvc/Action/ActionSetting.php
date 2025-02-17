<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Action;

use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;
use PeServer\Core\Text;

/**
 * HTTPメソッドとコントローラメソッドの紐づけ。
 */
readonly class ActionSetting
{
	/**
	 * 生成。
	 *
	 * @param string $controllerMethod コントローラの呼び出しメソッド名。
	 * @param array<IMiddleware|class-string<IMiddleware>> $actionMiddleware アクションミドルウェア。
	 * @param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $shutdownMiddleware シャットダウンミドルウェア。
	 */
	public function __construct(
		public string $controllerMethod,
		public array $actionMiddleware,
		public array $shutdownMiddleware
	) {
	}

	#region function

	public static function none(): ActionSetting
	{
		return new ActionSetting(Text::EMPTY, [], []);
	}

	#endregion
}
