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
		/** @readonly */
		public HttpStatus $status,
		/** @readonly */
		public string $className,
		/** @readonly */
		public ActionSetting $actionSetting,
		/** @readonly */
		public array $params
	) {
	}
}
