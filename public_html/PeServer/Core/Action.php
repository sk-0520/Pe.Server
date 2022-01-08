<?php

declare(strict_types=1);

namespace PeServer\Core;

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
	 * @var array<string,array{method:string,middleware:array<IMiddleware|string>,shutdown_middleware:array<IShutdownMiddleware|string>}>
	 */
	private $map = array();

	/**
	 * 追加。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 * @param array<IMiddleware|string> $middleware
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware
	 */
	public function add(HttpMethod $httpMethod, string $callMethod, array $middleware, array $shutdownMiddleware): void
	{
		foreach ($httpMethod->methods() as $method) {
			$this->map[$method] = [
				'method' => $callMethod,
				'middleware' => $middleware,
				'shutdown_middleware' => $shutdownMiddleware,
			];
		}
	}

	/**
	 * 取得。
	 *
	 * @param httpMethod $httpMethod HTTPメソッド
	 * @return array{method:string,middleware:array<IMiddleware|string>,shutdown_middleware:array<IShutdownMiddleware|string>}>|null あった場合はクラスメソッド、なければ null
	 */
	public function get(httpMethod $httpMethod): ?array
	{
		if (ArrayUtility::tryGet($this->map, $httpMethod->methods()[0], $result)) {
			return $result;
		}

		return null;
	}
}
