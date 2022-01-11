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
	 * @param HttpMethod|HttpMethod[] $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 * @param array<IMiddleware|string> $middleware
	 * @param array<IShutdownMiddleware|string> $shutdownMiddleware
	 */
	public function add(HttpMethod|array $httpMethod, string $callMethod, array $middleware, array $shutdownMiddleware): void
	{
		if (is_array($httpMethod)) {
			foreach ($httpMethod as $method) {
				$this->map[$method->getKind()] = [
					'method' => $callMethod,
					'middleware' => $middleware,
					'shutdown_middleware' => $shutdownMiddleware,
				];
			}
		} else {
			$this->map[$httpMethod->getKind()] = [
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
		if (ArrayUtility::tryGet($this->map, $httpMethod->getKind(), $result)) {
			return $result;
		}

		return null;
	}
}
