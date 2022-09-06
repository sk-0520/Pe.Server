<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * HTTPメソッド。
 */
abstract class HttpMethod
{
	#region define

	/** GET 生文字列 */
	protected const HTTP_METHOD_GET = 'GET';
	/** POST 生文字列 */
	protected const HTTP_METHOD_POST = 'POST';
	/** PUT 生文字列 */
	protected const HTTP_METHOD_PUT = 'PUT';
	/** DELETE 生文字列 */
	protected const HTTP_METHOD_DELETE = 'DELETE';
	/** HEAD 生文字列 */
	protected const HTTP_METHOD_HEAD = 'HEAD';
	/** OPTIONS 生文字列 */
	protected const HTTP_METHOD_OPTIONS = 'OPTIONS';
	/** PATCH 生文字列 */
	protected const HTTP_METHOD_PATCH = 'PATCH';
	/** TRACE 生文字列 */
	protected const HTTP_METHOD_TRACE = 'TRACE';

	#endregion

	#region variable

	/**
	 * 文字列にしたかったけど型弱すぎてね。
	 *
	 * もう文字列で対応してもいいような気がせんでもないけどしゃあない。
	 *
	 * @var array<string,HttpMethod>
	 * @phpstan-var array<self::HTTP_METHOD_*,HttpMethod>
	 */
	private static array $cacheKinds;

	#endregion

	#region function

	/**
	 * 生成。
	 *
	 * @param string ...$httpMethodKinds
	 * @phpstan-param self::HTTP_METHOD_* ...$httpMethodKinds
	 * @return HttpMethod[]
	 */
	public static function create(string ...$httpMethodKinds): array
	{
		$result = [];
		foreach ($httpMethodKinds as $httpMethodKind) {
			$result[] = new LocalHttpMethodImpl($httpMethodKind);
		}

		return $result;
	}

	/**
	 * 要求HTTPメソッドから HttpMethod を生成。
	 *
	 * @param string $requestMethod
	 * @return HttpMethod
	 */
	public static function from(string $requestMethod): HttpMethod
	{
		//@phpstan-ignore-next-line
		return new LocalHttpMethodImpl(Text::toUpper(Text::trim($requestMethod)));
	}

	/**
	 * Undocumented function
	 *
	 * @param string $value
	 * @phpstan-param self::HTTP_METHOD_* $value
	 * @return HttpMethod
	 */
	private static function cache(string $value): HttpMethod
	{
		if (!isset(self::$cacheKinds[$value])) {
			self::$cacheKinds[$value] = new LocalHttpMethodImpl($value);
		}

		return self::$cacheKinds[$value];
	}

	/**
	 * @return HttpMethod[] GET + HEAD
	 */
	public static function gets(): array
	{
		return [self::get(), self::head()];
	}

	/**
	 * @return HttpMethod GET
	 */
	public static function get(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_GET);
	}
	/**
	 * @return HttpMethod POST
	 */
	public static function post(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_POST);
	}
	/**
	 * @return HttpMethod PUT
	 */
	public static function put(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_PUT);
	}
	/**
	 * @return HttpMethod DELETE
	 */
	public static function delete(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_DELETE);
	}
	/**
	 * @return HttpMethod HEAD
	 */
	public static function head(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_HEAD);
	}
	/**
	 * @return HttpMethod OPTIONS
	 */
	public static function options(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_OPTIONS);
	}
	/**
	 * @return HttpMethod PATCH
	 */
	public static function patch(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_PATCH);
	}
	/**
	 * @return HttpMethod TRACE
	 */
	public static function trace(): HttpMethod
	{
		return self::cache(self::HTTP_METHOD_TRACE);
	}

	public abstract function getKind(): string;

	public abstract function is(HttpMethod $httpMethod): bool;

	#endregion
}

/**
 * アプリ側からは使用しない。
 */
final class LocalHttpMethodImpl extends HttpMethod
{
	/**
	 * 生成。
	 *
	 * @param string $kind HTTPメソッド種別。
	 * @phpstan-param parent::HTTP_METHOD_* $kind
	 */
	public function __construct(
		/** @readonly */
		private string $kind
	) {
		$methods = [
			parent::HTTP_METHOD_GET,
			parent::HTTP_METHOD_POST,
			parent::HTTP_METHOD_PUT,
			parent::HTTP_METHOD_DELETE,
			parent::HTTP_METHOD_HEAD,
			parent::HTTP_METHOD_OPTIONS,
			parent::HTTP_METHOD_PATCH,
			parent::HTTP_METHOD_TRACE,
		];

		if (!Arr::in($methods, $this->kind)) {
			throw new ArgumentException($this->kind);
		}
	}

	public function getKind(): string
	{
		return $this->kind;
	}

	public function is(HttpMethod $httpMethod): bool
	{
		if ($httpMethod instanceof LocalHttpMethodImpl) {
			return $this->kind === $httpMethod->kind;
		}

		return $this->kind === $httpMethod->getKind();
	}
}
