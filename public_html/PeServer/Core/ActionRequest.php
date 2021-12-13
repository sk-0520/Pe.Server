<?php

declare(strict_types=1);

namespace PeServer\Core;

use \LogicException;

/**
 * アクションに対するリクエストデータ。
 *
 * GET/POST/URLパラメータの値などはこいつから取得する。
 */
class ActionRequest
{
	/**
	 * Undocumented variable
	 *
	 * @var array<string,string>
	 */
	private $urlRequests;

	/**
	 * リクエストデータ構築
	 *
	 * @param array<string,string> $urlRequests URLパラメータ
	 */
	public function __construct(array $urlRequests)
	{
		$this->urlRequests  = $urlRequests;
	}

	/**
	 * キーに対する値が存在するか。
	 *
	 * @param string $key キー
	 * @return array{exists:bool,type:string}
	 */
	public function exists(string $key): array
	{
		//TODO: $urlRequests

		if (isset($_GET[$key])) {
			return ['exists' => true, 'type' => 'get'];
		}

		if (isset($_POST[$key])) {
			return ['exists' => true, 'type' => 'post'];
		}

		if (isset($_FILES[$key])) {
			return ['exists' => true, 'type' => 'file'];
		}

		return ['exists' => false, 'type' => 'none'];
	}

	// public function isMulti(string $key): bool
	// {

	// }

	/**
	 * キーに対する値を取得する。
	 *
	 * ファイルは取得できない。
	 *
	 * @param string $key
	 * @return string
	 * @throws LogicException キーに対する値が存在しない。
	 */
	public function getValue(string $key): string
	{
		//TODO: $urlRequests

		if (isset($_GET[$key])) {
			return $_GET[$key];
		}

		if (isset($_POST[$key])) {
			return $_POST[$key];
		}

		throw new LogicException("parameter not found: $key");
	}

	// public function gets($key): array
	// {
	// }

	// public function file($key): array
	// {
	// }
}
