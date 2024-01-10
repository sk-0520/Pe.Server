<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * ルーティングのアクション設定。
 */
readonly class RouteAction
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param class-string<ControllerBase> $className
	 * @param ActionSetting $actionSetting
	 * @param array<non-empty-string,string> $params パラメータ。
	 */
	public function __construct(
		public HttpStatus $status,
		public string $className,
		public ActionSetting $actionSetting,
		public array $params
	) {
	}
}
