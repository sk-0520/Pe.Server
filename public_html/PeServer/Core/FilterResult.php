<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use PeServer\Core\UrlUtility;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * フィルタリング結果。
 */
abstract class FilterResult
{
	public const RESULT_KIND_NONE = 0;
	public const RESULT_KIND_STATUS = 1;

	private static ?FilterResult $none;
	public static function none(): FilterResult
	{
		return self::$none ??= new class extends FilterResult
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
	 * Undocumented function
	 *
	 * @param string $path
	 * @param array<string,string>|null $query
	 * @param HttpStatus|null $status
	 * @return FilterResult
	 */
	public static function redirect(string $path, ?array $query = null, ?HttpStatus $status = null): FilterResult
	{
		if (Regex::isMatch($path, '|(https?:)?//|')) {
			throw new ArgumentException();
		}

		$url = UrlUtility::buildPath($path, $query ?? []);

		return new _FilterRedirectResult($status ?? HttpStatus::found(), $url);
	}

	public static function error(HttpStatus $status, string $message = ''): FilterResult
	{
		return new _FilterErrorResult($status, $message);
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

class _FilterRedirectResult extends FilterResult
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
		header('Location: ' . $this->url, true, $this->status->code());
	}
}

class _FilterErrorResult extends FilterResult
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
