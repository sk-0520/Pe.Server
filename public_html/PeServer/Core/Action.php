<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * HTTPメソッドとコントローラメソッドを紐づける。
 * リクエストパスとコントローラクラス自体の紐づけは Route を参照のこと。
 */
class Action
{
	/**
	 * HTTPメソッド。
	 *
	 * @var string
	 */
	public $httpMethod;
	/**
	 * コントローラメソッド。
	 *
	 * @var string
	 */
	public $callMethod;

	/**
	 * Undocumented function
	 *
	 * @param string $httpMethod HTTPメソッド
	 * @param string $callMethod コントローラメソッド。
	 */
	public function __construct(string $httpMethod, string $callMethod)
	{
		$this->httpMethod = $httpMethod;
		$this->callMethod = $callMethod;
	}
}
