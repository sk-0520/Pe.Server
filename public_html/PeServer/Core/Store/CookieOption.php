<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateTimeImmutable;
use \DateInterval;

class CookieOption
{
	/**
	 * Undocumented function
	 *
	 * @param string $path
	 * @param DateInterval|null $span
	 * @param boolean $secure
	 * @param boolean $httpOnly
	 * @param 'Lax'|'lax'|'None'|'none'|'Strict'|'strict' $sameSite
	 * @return CookieOption
	 */
	public static function create(string $path, ?DateInterval $span, bool $secure, bool $httpOnly, string $sameSite): CookieOption
	{
		$option = new self();

		$option->path = $path;
		$option->span = $span;
		$option->secure = $secure;
		$option->httpOnly = $httpOnly;
		$option->sameSite = $sameSite;

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
	 * Undocumented variable
	 *
	 * @var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict'
	 */
	public string $sameSite;

	/**
	 * cookie の寿命を数値に変換。
	 *
	 * @return integer
	 */
	public function getExpires(): int
	{
		if (is_null($this->span)) {
			return 0;
		}

		$reference = new DateTimeImmutable;
		$endTime = $reference->add($this->span);

		$result = $endTime->getTimestamp() - $reference->getTimestamp();

		return $result + time();
	}
}
