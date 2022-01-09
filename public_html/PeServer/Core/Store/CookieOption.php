<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;

class CookieOption
{
	public static function create(string $path, ?DateInterval $span, bool $secure, bool $httpOnly): CookieOption
	{
		$option = new self();

		$option->path = $path;
		$option->span = $span;
		$option->secure = $secure;
		$option->httpOnly = $httpOnly;

		return $option;
	}

	/**
	 * パス
	 *
	 * @var string
	 */
	public string $path;
	/**
	 * 期限。
	 *
	 * 未設定で 0 扱い。
	 *
	 * @var DateInterval|null
	 */
	public ?DateInterval $span;
	/**
	 * HTTPS に限定。
	 *
	 * @var boolean
	 */
	public bool $secure;
	/**
	 *  HTTP リクエストのみで使用。
	 *
	 * @var boolean
	 */
	public bool $httpOnly;

	/**
	 * cookie の寿命を数値に変換。
	 *
	 * @return integer
	 */
	public function getTotalMinutes(): int
	{
		if (is_null($this->span)) {
			return 0;
		}

		$result = ($this->span->d * 24 * 60) + ($this->span->h * 60) + $this->span->i;
		return $result;
	}
}
