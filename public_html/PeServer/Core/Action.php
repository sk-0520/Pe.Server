<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\HttpMethod;
use PeServer\Core\Mvc\Middleware\IMiddleware;

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
	 * @var array<string,array{method:string,middleware:array<IMiddleware|string>}>
	 */
	private $map = array();

	/**
	 * 追加。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 * @param array<IMiddleware|string> $middleware
	 */
	public function add(HttpMethod $httpMethod, string $callMethod, array $middleware): void
	{
		foreach ($httpMethod->methods() as $method) {
			$this->map[$method] = [
				'method' => $callMethod,
				'middleware' => $middleware,
			];
		}
	}

	/**
	 * 取得。
	 *
	 * @param string $httpMethod HTTPメソッド
	 * @return array{method:string,middleware:array<IMiddleware|string>}>|null あった場合はクラスメソッド、なければ null
	 */
	public function get(string $httpMethod): ?array
	{
		if (ArrayUtility::tryGet($this->map, $httpMethod, $result)) {
			return $result;
		}

		return null;
	}
}
