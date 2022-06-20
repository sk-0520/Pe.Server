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
	/**
	 * 生成。
	 *
	 * @param HttpMethod $httpMethod 要求メソッド。
	 * @param HttpHeader $httpHeader 要求ヘッダ。
	 * @param array<string,string> $urlParameters URLパラメータ。
	 */
	public function __construct(
		public HttpMethod $httpMethod,
		public HttpHeader $httpHeader,
		protected array $urlParameters
	) {
	}

	/**
	 * 名前に対する値が存在するか。
	 *
	 * @param string $name パラメータ名。
	 * @param bool $strict メソッドを厳格に判定するか。
	 * @return HttpRequestExists
	 */
	public function exists(string $name, bool $strict = true): HttpRequestExists
	{
		if (isset($this->urlParameters[$name])) {
			return new HttpRequestExists($name, true, HttpRequestExists::KIND_URL);
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::get())) {
			if (isset($_GET[$name])) {
				return new HttpRequestExists($name, true, HttpRequestExists::KIND_GET);
			}
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::post())) {
			if (isset($_POST[$name])) {
				return new HttpRequestExists($name, true, HttpRequestExists::KIND_POST);
			}
			if (isset($_FILES[$name])) {
				return new HttpRequestExists($name, true, HttpRequestExists::KIND_FILE);
			}
		}

		return new HttpRequestExists($name, false, HttpRequestExists::KIND_NONE);
	}

	// public function isMulti(string $key): bool
	// {

	// }

	/**
	 * キーに対する値を取得する。
	 *
	 * ファイルは取得できない。
	 *
	 * @param string $name
	 * @return string
	 * @throws KeyNotFoundException キーに対する値が存在しない。
	 */
	public function getValue(string $name, bool $strict = true): string
	{
		if (isset($this->urlParameters[$name])) {
			return $this->urlParameters[$name];
		}

		if (!$strict || $this->httpMethod->is(HttpMethod::get())) {
			if (isset($_GET[$name])) {
				return $_GET[$name];
			}
		}
		if (!$strict || $this->httpMethod->is(HttpMethod::post())) {

			if (isset($_POST[$name])) {
				return $_POST[$name];
			}
		}

		throw new KeyNotFoundException("parameter not found: $name");
	}

	// public function gets($name): array
	// {
	// }

	// public function file($name): array
	// {
	// }
}
