<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use DateInterval;
use DateTimeImmutable;

/**
 * Cookie 設定。
 */
class CookieOptions
{
	#region variable

	/**
	 * パス
	 *
	 * @var non-empty-string
	 */
	public readonly string $path;
	/**
	 * 期限。
	 */
	public ?DateInterval $span;
	/**
	 * HTTPS に限定。
	 */
	public readonly bool $secure;
	/**
	 *  HTTP リクエストのみで使用。
	 */
	public readonly bool $httpOnly;

	/**
	 * 同じサイト。
	 *
	 * @var string
	 * @phpstan-var globa-alias-cookie-same-site
	 */
	public readonly string $sameSite;

	#endregion

	/**
	 * 生成。
	 *
	 * @param non-empty-string $path パス。
	 * @param DateInterval|null $span 期間。
	 * @param boolean $secure HTTPS に限定するか。
	 * @param boolean $httpOnly HTTP リクエストのみで使用するか。
	 * @param string $sameSite 同じサイト。
	 * @phpstan-param globa-alias-cookie-same-site $sameSite
	 */
	public function __construct(string $path, ?DateInterval $span, bool $secure, bool $httpOnly, string $sameSite)
	{
		$this->path = $path;
		$this->span = $span;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
		$this->sameSite = $sameSite;
	}

	#region function

	/**
	 * cookie の寿命を数値に変換。
	 *
	 * @return int
	 */
	public function getExpires(): int
	{
		if ($this->span === null) {
			return 0;
		}

		$reference = new DateTimeImmutable();
		$endTime = $reference->add($this->span);

		$result = $endTime->getTimestamp() - $reference->getTimestamp();

		return $result + time();
	}

	#endregion
}
