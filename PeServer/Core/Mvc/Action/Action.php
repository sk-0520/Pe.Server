<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Action;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\Action\ActionSetting;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * HTTPメソッドとコントローラメソッドを紐づける。
 * リクエストパスとコントローラクラス自体の紐づけは Route を参照のこと。
 */
class Action
{
	#region variable

	/**
	 * 紐づけ。
	 *
	 * HTTPメソッドとコントローラメソッドがペアになる。
	 * 後入れ優先。
	 *
	 * @var array<string,ActionSetting>
	 * @phpstan-var array<HttpMethod::HTTP_METHOD_*|non-empty-string,ActionSetting>
	 */
	private array $map = [];

	#endregion

	#region function

	/**
	 * 追加。
	 *
	 * @param HttpMethod|HttpMethod[] $httpMethod HTTPメソッド
	 * @param non-empty-string $callMethod コントローラメソッド。
	 * @param array<IMiddleware|class-string<IMiddleware>> $middleware
	 * @param array<IShutdownMiddleware|class-string<IShutdownMiddleware>> $shutdownMiddleware
	 */
	public function add(HttpMethod|array $httpMethod, string $callMethod, array $middleware, array $shutdownMiddleware): void
	{
		if (is_array($httpMethod)) {
			foreach ($httpMethod as $method) {
				$this->map[$method->name] = new ActionSetting(
					$callMethod,
					$middleware,
					$shutdownMiddleware
				);
			}
		} else {
			$this->map[$httpMethod->name] = new ActionSetting(
				$callMethod,
				$middleware,
				$shutdownMiddleware
			);
		}
	}

	/**
	 * 取得。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド
	 * @return ActionSetting|null あった場合はクラスメソッド紐づけ情報、なければ null
	 */
	public function get(HttpMethod $httpMethod): ?ActionSetting
	{
		if (Arr::tryGet($this->map, $httpMethod->name, $result)) {
			return $result;
		}

		return null;
	}

	#endregion
}
