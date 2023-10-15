<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\Core\Store\CookieOptions;

/**
 * Cookie設定。
 *
 * @immutable
 */
class CookieStoreSetting
{
	#region variable

	public ?string $span = null;
	public ?string $path = null;
	public ?bool $secure = null;
	public ?bool $httpOnly = null;
	/**
	 * @var string|null
	 * @phpstan-var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict'|null
	 */
	public ?string $sameSite = null;

	#endregion

	public function __construct(?CookieOptions $option = null)
	{
		if ($option !== null) {
			if ($option->span !== null) {
				$this->span = $option->span->format('P%yY%mM%dDT%hH%iM%sS');
			}
			$this->path = $option->path;
			$this->secure = $option->secure;
			$this->httpOnly = $option->httpOnly;
			$this->sameSite = $option->sameSite;
		}
	}
}
