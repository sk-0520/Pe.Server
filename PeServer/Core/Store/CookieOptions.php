<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use DateInterval;
use DateTimeImmutable;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Throws\ArgumentException;

/**
 * Cookie 設定。
 *
 * @phpstan-type SameSiteAlias "Lax"|"lax"|"None"|"none"|"Strict"|"strict"
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
	 * @phpstan-var SameSiteAlias
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
	 */
	public function __construct(string $path, ?DateInterval $span, bool $secure, bool $httpOnly, string $sameSite)
	{
		$this->path = $path;
		$this->span = $span;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
		if (self::isSameSite($sameSite)) {
			$this->sameSite = $sameSite;
		} else {
			throw new ArgumentException('$sameSite is ' . $sameSite);
		}
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

	/**
	 * SameSite 判定。
	 *
	 * @param string $value
	 * @return bool
	 * @phpstan-assert-if-true SameSiteAlias $value
	 */
	public static function isSameSite(string $value): bool
	{
		$ss = ["Lax", "lax", "None", "none", "Strict", "strict"];
		return Arr::in($ss, $value);
	}

	#endregion
}
