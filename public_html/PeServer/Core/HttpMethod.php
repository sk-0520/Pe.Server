<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\HttpStatusException;

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
	/** HEAD 生文字列 */
	public const HEAD = 'HEAD';
	/** OPTIONS 生文字列 */
	public const OPTIONS = 'OPTIONS';
	/** PATCH 生文字列 */
	public const PATCH = 'PATCH';
	/** TRACE 生文字列 */
	public const TRACE = 'TRACE';

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
	 * @return HttpMethod HEAD
	 */
	public static function head(): HttpMethod
	{
		return self::create(self::HEAD);
	}
	/**
	 * @return HttpMethod OPTIONS
	 */
	public static function options(): HttpMethod
	{
		return self::create(self::OPTIONS);
	}
	/**
	 * @return HttpMethod PATCH
	 */
	public static function patch(): HttpMethod
	{
		return self::create(self::PATCH);
	}
	/**
	 * @return HttpMethod TRACE
	 */
	public static function trace(): HttpMethod
	{
		return self::create(self::TRACE);
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
			parent::GET,
			parent::POST,
			parent::PUT,
			parent::DELETE,
			parent::HEAD,
			parent::OPTIONS,
			parent::PATCH,
			parent::TRACE,
		]);
		foreach ($methods as $method) {
			if (!$safeMethods->any(function ($i) use ($method) {
				return $i === $method;
			})) {
				throw new HttpStatusException(HttpStatus::methodNotAllowed());
			}
		}

		$this->methods = $methods;
	}

	public function methods(): array
	{
		return $this->methods;
	}
}
