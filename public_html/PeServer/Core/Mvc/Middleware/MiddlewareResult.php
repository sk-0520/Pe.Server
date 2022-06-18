<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Regex;
use PeServer\Core\UrlUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * ミドルウェア結果。
 */
abstract class MiddlewareResult
{
	public const RESULT_KIND_NONE = 0;
	public const RESULT_KIND_STATUS = 1;

	private static ?MiddlewareResult $none;
	public static function none(): MiddlewareResult
	{
		return self::$none ??= new class extends MiddlewareResult
		{
			public function __construct()
			{
				parent::__construct(parent::RESULT_KIND_NONE);
			}

			public function apply(): void
			{
				// こいつがここまでくればバグってる
				throw new InvalidOperationException();
			}
		};
	}

	/**
	 * リダイレクト処理生成。
	 *
	 * @param string $path
	 * @param array<string,string>|null $query
	 * @param HttpStatus|null $status
	 * @return MiddlewareResult
	 */
	public static function redirect(string $path, ?array $query = null, ?HttpStatus $status = null): MiddlewareResult
	{
		if (Regex::isMatch($path, '|(https?:)?//|')) {
			throw new ArgumentException();
		}

		$url = UrlUtility::buildPath($path, $query ?? []);

		return new _RedirectMiddlewareResult($status ?? HttpStatus::found(), $url);
	}

	/**
	 * エラー処理生成。
	 *
	 * @param HttpStatus $status
	 * @param string $message
	 * @return MiddlewareResult
	 */
	public static function error(HttpStatus $status, string $message = InitialValue::EMPTY_STRING): MiddlewareResult
	{
		return new _ErrorMiddlewareResult($status, $message);
	}

	private int $kind;
	protected function __construct(int $kind)
	{
		$this->kind = $kind;
	}

	public final function canNext(): bool
	{
		return $this->kind === self::RESULT_KIND_NONE;
	}

	public abstract function apply(): void;
}

class _RedirectMiddlewareResult extends MiddlewareResult
{
	private HttpStatus $status;
	private string $url;

	public function __construct(HttpStatus $status, string $url)
	{
		parent::__construct(parent::RESULT_KIND_STATUS);

		$this->status = $status;
		$this->url = $url;
	}

	public function apply(): void
	{
		header('Location: ' . $this->url, true, $this->status->getCode());
	}
}

class _ErrorMiddlewareResult extends MiddlewareResult
{
	private HttpStatus $status;
	private string $message;

	public function __construct(HttpStatus $status, string $message)
	{
		parent::__construct(parent::RESULT_KIND_STATUS);

		$this->status = $status;
		$this->message = $message;
	}

	public function apply(): void
	{
		throw new HttpStatusException($this->status, $this->message);
	}
}
