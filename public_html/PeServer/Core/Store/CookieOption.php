<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;
use \DateTimeImmutable;

/**
 * Cookie 設定。
 */
class CookieOption
{
	/**
	 * 生成。
	 *
	 * @param string $path パス。
	 * @param DateInterval|null $span 期間。
	 * @param boolean $secure HTTPS に限定するか。
	 * @param boolean $httpOnly HTTP リクエストのみで使用するか。
	 * @param 'Lax'|'lax'|'None'|'none'|'Strict'|'strict' $sameSite 同じサイト。
	 */
	public function __construct(string $path, ?DateInterval $span, bool $secure, bool $httpOnly, string $sameSite)
	{
		$this->path = $path;
		$this->span = $span;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
		$this->sameSite = $sameSite;
	}

	/**
	 * パス
	 *
	 * @var string
	 * @readonly
	 */
	public string $path;
	/**
	 * 期限。
	 *
	 * 未設定で 0 扱い。
	 */
	public ?DateInterval $span;
	/**
	 * HTTPS に限定。
	 *
	 * @readonly
	 */
	public bool $secure;
	/**
	 *  HTTP リクエストのみで使用。
	 *
	 * @readonly
	 */
	public bool $httpOnly;

	/**
	 * 同じサイト。
	 *
	 * @var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict'
	 * @readonly
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

		$reference = new DateTimeImmutable();
		$endTime = $reference->add($this->span);

		$result = $endTime->getTimestamp() - $reference->getTimestamp();

		return $result + time();
	}
}
