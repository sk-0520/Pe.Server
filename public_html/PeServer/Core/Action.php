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
	 * @var array<string,string>
	 */
	private $map = array();

	/**
	 * 追加。
	 *
	 * @param HttpMethod $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 */
	public function add(HttpMethod $httpMethod, string $callMethod): void
	{
		foreach($httpMethod->values() as $value) {
			$this->map[$value] = $callMethod;
		}
	}

	/**
	 * 取得。
	 *
	 * @param string $httpMethod HTTPメソッド
	 * @return string|null あった場合はクラスメソッド、なければ null
	 */
	public function get(string $httpMethod): ?string
	{
		if (ArrayUtility::tryGet($this->map, $httpMethod, $result)) {
			return $result;
		}

		return null;
	}
}
