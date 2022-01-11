<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Throws\KeyNotFoundException;

/**
 * HTTPリクエストデータ。
 *
 * GET/POST/URLパラメータの値などはこいつから取得する。
 */
class HttpRequest
{
	public const REQUEST_NONE = 'none';
	public const REQUEST_URL = 'url';
	public const REQUEST_GET = 'get';
	public const REQUEST_POST = 'post';
	public const REQUEST_FILE = 'file';

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
	 * @return array{exists:bool,type:string}
	 */
	public function exists(string $key, bool $strict = true): array
	{
		if (isset($this->urlParameters[$key])) {
			return ['exists' => true, 'type' => self::REQUEST_URL];
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::GET)) {
			if (isset($_GET[$key])) {
				return ['exists' => true, 'type' => self::REQUEST_GET];
			}
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::POST)) {
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

		if (!$strict || $this->httpMethod->is(HttpMethod::GET)) {
			if (isset($_GET[$key])) {
				return $_GET[$key];
			}
		}
		if (!$strict || $this->httpMethod->is(HttpMethod::POST)) {

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
