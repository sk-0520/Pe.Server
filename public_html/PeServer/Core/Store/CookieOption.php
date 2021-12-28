<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;

class CookieOption
{
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

	public function __construct(string $path, ?DateInterval $span, bool $secure, bool $httpOnly)
	{
		$this->path = $path;
		$this->span = $span;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

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

		return ($this->span->d * 24 * 60) + ($this->span->h * 60) + $this->span->i;
	}
}
