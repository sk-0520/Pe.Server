<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\ActionSetting;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * HTTPメソッドとコントローラメソッドを紐づける。
 * リクエストパスとコントローラクラス自体の紐づけは Route を参照のこと。
 */
class Action
{
	/**
	 * 紐づけ。
	 *
	 * HTTPメソッドとコントローラメソッドがペアになる。
	 * 後入れ優先。
	 *
	 * @var array<string,ActionSetting>
	 */
	private array $map = array();

	/**
	 * 追加。
	 *
	 * @param HttpMethod|HttpMethod[] $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 * @param array<IMiddleware|string> $middleware
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware
	 */
	public function add(HttpMethod|array $httpMethod, string $callMethod, array $middleware, array $shutdownMiddleware): void
	{
		if (is_array($httpMethod)) {
			foreach ($httpMethod as $method) {
				$this->map[$method->getKind()] = new ActionSetting(
					$callMethod,
					$middleware,
					$shutdownMiddleware
				);
			}
		} else {
			$this->map[$httpMethod->getKind()] = new ActionSetting(
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
		if (ArrayUtility::tryGet($this->map, $httpMethod->getKind(), $result)) {
			return $result;
		}

		return null;
	}
}
