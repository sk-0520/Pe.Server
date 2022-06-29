<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;


/**
 * ルーティングのアクション設定。
 */
class RouteAction
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $status
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param ActionSetting $actionSetting
	 * @param array<string,string> $params パラメータ。
	 */
	public function __construct(
		public HttpStatus $status,
		public string $className,
		public ActionSetting $actionSetting,
		public array $params
	) {
	}
}
