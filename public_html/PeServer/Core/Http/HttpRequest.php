<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Throws\KeyNotFoundException;

/**
 * HTTPリクエストデータ。
 *
 * GET/POST/URLパラメータの値などはこいつから取得する。
 */
class HttpRequest
{
	public const REQUEST_NONE = 0;
	public const REQUEST_URL = 1;
	public const REQUEST_GET = 2;
	public const REQUEST_POST = 3;
	public const REQUEST_FILE = 4;

	public HttpMethod $httpMethod;
	public HttpHeader $httpHeader;

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
	public function __construct(HttpMethod $httpMethod, HttpHeader $httpHeader, array $urlParameters)
	{
		$this->httpMethod = $httpMethod;
		$this->httpHeader = $httpHeader;
		$this->urlParameters = $urlParameters;
	}

	/**
	 * キーに対する値が存在するか。
	 *
	 * @param string $key キー
	 * @return array{exists:bool,type:int}
	 */
	public function exists(string $key, bool $strict = true): array
	{
		if (isset($this->urlParameters[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_URL];
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::get())) {
			if (isset($_GET[$key])) {
				return ['exists' => true, 'type' => self::REQUEST_GET];
			}
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::post())) {
			if (isset($_POST[$key])) {
				return ['exists' => true, 'type' => self::REQUEST_POST];
			}
			if (isset($_FILES[$key])) {
				return ['exists' => true, 'type' => self::REQUEST_FILE];
			}
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
	public function getValue(string $key, bool $strict = true): string
	{
		if (isset($this->urlParameters[$key])) {
			return $this->urlParameters[$key];
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::get())) {
			if (isset($_GET[$key])) {
				return $_GET[$key];
			}
		}
		if (!$strict || $this->httpMethod->is(HttpMethod::post())) {

			if (isset($_POST[$key])) {
				return $_POST[$key];
			}
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
