<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\StringUtility;


/**
 * HTTPメソッド。
 */
abstract class HttpMethod
{
	/** GET 生文字列 */
	private const GET = 'GET';
	/** POST 生文字列 */
	private const POST = 'POST';
	/** PUT 生文字列 */
	private const PUT = 'PUT';
	/** DELETE 生文字列 */
	private const DELETE = 'DELETE';
	/** HEAD 生文字列 */
	private const HEAD = 'HEAD';
	/** OPTIONS 生文字列 */
	private const OPTIONS = 'OPTIONS';
	/** PATCH 生文字列 */
	private const PATCH = 'PATCH';
	/** TRACE 生文字列 */
	private const TRACE = 'TRACE';

	/**
	 * Undocumented variable
	 *
	 * @var array<string,HttpMethod>
	 */
	private static array $cacheKinds;

	/**
	 * 生成。
	 *
	 * @param string ...$httpMethodKinds
	 * @return HttpMethod[]
	 */
	public static function create(string ...$httpMethodKinds): array
	{
		$result = [];
		foreach ($httpMethodKinds as $httpMethodKind) {
			$result[] = new _HttpMethod_Impl($httpMethodKind);
		}

		return $result;
	}

	public static function from(string $method): HttpMethod
	{
		return new _HttpMethod_Impl(StringUtility::toUpper(StringUtility::trim($method)));
	}

	private static function cache(string $value): HttpMethod
	{
		if (!isset(self::$cacheKinds[$value])) {
			self::$cacheKinds[$value] = new _HttpMethod_Impl($value);
		}

		return self::$cacheKinds[$value];
	}

	/**
	 * @return HttpMethod GET
	 */
	public static function get(): HttpMethod
	{
		return self::cache(self::GET);
	}
	/**
	 * @return HttpMethod POST
	 */
	public static function post(): HttpMethod
	{
		return self::cache(self::POST);
	}
	/**
	 * @return HttpMethod PUT
	 */
	public static function put(): HttpMethod
	{
		return self::cache(self::PUT);
	}
	/**
	 * @return HttpMethod DELETE
	 */
	public static function delete(): HttpMethod
	{
		return self::cache(self::DELETE);
	}
	/**
	 * @return HttpMethod HEAD
	 */
	public static function head(): HttpMethod
	{
		return self::cache(self::HEAD);
	}
	/**
	 * @return HttpMethod OPTIONS
	 */
	public static function options(): HttpMethod
	{
		return self::cache(self::OPTIONS);
	}
	/**
	 * @return HttpMethod PATCH
	 */
	public static function patch(): HttpMethod
	{
		return self::cache(self::PATCH);
	}
	/**
	 * @return HttpMethod TRACE
	 */
	public static function trace(): HttpMethod
	{
		return self::cache(self::TRACE);
	}

	public abstract function getKind(): string;

	public abstract function is(HttpMethod $httpMethod): bool;
}

/**
 * アプリ側からは使用しない。
 */
final class _HttpMethod_Impl extends HttpMethod
{
	public string $kind;

	/**
	 * 生成。
	 *
	 * @param string $kind HTTPメソッド種別。
	 */
	public function __construct(string $kind)
	{
		$this->kind = $kind;
	}

	public function getKind(): string
	{
		return $this->kind;
	}

	public function is(HttpMethod $httpMethod): bool
	{
		if($httpMethod instanceof _HttpMethod_Impl) {
			return $this->kind === $httpMethod->kind;
		}

		return $this->kind === $httpMethod->getKind();
	}
}
