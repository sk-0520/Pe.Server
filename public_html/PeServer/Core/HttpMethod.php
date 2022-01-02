<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

/**
 * HTTPメソッド。
 */
abstract class HttpMethod
{
	/** GET 生文字列 */
	public const GET = 'GET';
	/** POST 生文字列 */
	public const POST = 'POST';
	/** PUT 生文字列 */
	public const PUT = 'PUT';
	/** DELETE 生文字列 */
	public const DELETE = 'DELETE';

	/**
	 * 生成。
	 *
	 * @param string ...$methods
	 * @return HttpMethod
	 */
	public static function create(string ...$methods): HttpMethod
	{
		return new _HttpMethod_Impl(...$methods);
	}

	/**
	 * @return HttpMethod GET
	 */
	public static function get(): HttpMethod
	{
		return self::create(self::GET);
	}
	/**
	 * @return HttpMethod POST
	 */
	public static function post(): HttpMethod
	{
		return self::create(self::POST);
	}
	/**
	 * @return HttpMethod PUT
	 */
	public static function put(): HttpMethod
	{
		return self::create(self::PUT);
	}
	/**
	 * @return HttpMethod DELETE
	 */
	public static function delete(): HttpMethod
	{
		return self::create(self::DELETE);
	}

	/**
	 * メソッド一覧を取得。
	 *
	 * @return string[]
	 */
	public abstract function methods(): array;
}

/**
 * アプリ側からは使用しない。
 */
class _HttpMethod_Impl extends HttpMethod
{
	/** @var string[] */
	private $methods;

	/**
	 * 生成。
	 *
	 * @param string ...$methods HTTPメソッド。
	 */
	public function __construct(string ...$methods)
	{
		$safeMethods = Collection::from([
			self::GET,
			self::POST,
			self::PUT,
			self::DELETE,
		]);
		foreach ($methods as $method) {
			if (!$safeMethods->any(function ($i) use ($method) {
				return $i === $method;
			})) {
				throw new ArgumentException("HTTP METHOD: $method");
			}
		}

		$this->methods = $methods;
	}

	public function methods(): array
	{
		return $this->methods;
	}
}
