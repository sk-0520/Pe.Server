<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Throws\KeyNotFoundException;

/**
 * アクションに対するリクエストデータ。
 *
 * GET/POST/URLパラメータの値などはこいつから取得する。
 */
class ActionRequest
{
	public const REQUEST_NONE = 'none';
	public const REQUEST_URL = 'url';
	public const REQUEST_GET = 'get';
	public const REQUEST_POST = 'post';
	public const REQUEST_FILE = 'file';

	/**
	 * URLパラメータ。
	 *
	 * @var array<string,string>
	 */
	private $urlParameters;

	/**
	 * リクエストデータ構築
	 *
	 * @param array<string,string> $urlParameters URLパラメータ
	 */
	public function __construct(array $urlParameters)
	{
		$this->urlParameters = $urlParameters;
	}

	/**
	 * キーに対する値が存在するか。
	 *
	 * @param string $key キー
	 * @return array{exists:bool,type:string}
	 */
	public function exists(string $key): array
	{
		if (isset($this->urlParameters[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_URL];
		}

		if (isset($_GET[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_GET];
		}

		if (isset($_POST[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_POST];
		}

		if (isset($_FILES[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_FILE];
		}

		return ['exists' => false, 'type' => self::REQUEST_NONE];
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
	 * @throws KeyNotFoundException キーに対する値が存在しない。
	 */
	public function getValue(string $key): string
	{
		if (isset($this->urlParameters[$key])) {
			return $this->urlParameters[$key];
		}

		if (isset($_GET[$key])) {
			return $_GET[$key];
		}

		if (isset($_POST[$key])) {
			return $_POST[$key];
		}

		throw new KeyNotFoundException("parameter not found: $key");
	}

	// public function gets($key): array
	// {
	// }

	// public function file($key): array
	// {
	// }
}
