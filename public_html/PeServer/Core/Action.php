<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\HttpMethod;

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
	 * @var array<string,array{method:string,options:ActionOptions}>
	 */
	private $map = array();

	/**
	 * 追加。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 */
	public function add(HttpMethod $httpMethod, string $callMethod, ActionOptions $options): void
	{
		foreach ($httpMethod->methods() as $method) {
			$this->map[$method] = [
				'method' => $callMethod,
				'options' => $options,
			];
		}
	}

	/**
	 * 取得。
	 *
	 * @param string $httpMethod HTTPメソッド
	 * @return array{method:string,options:ActionOptions}>|null あった場合はクラスメソッド、なければ null
	 */
	public function get(string $httpMethod): ?array
	{
		if (ArrayUtility::tryGet($this->map, $httpMethod, $result)) {
			return $result;
		}

		return null;
	}
}
