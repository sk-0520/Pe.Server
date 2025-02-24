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

	public ?string $span = null; //@phpstan-ignore-line [CODE_READONLY]
	public ?string $path = null; //@phpstan-ignore-line [CODE_READONLY]
	public ?bool $secure = null; //@phpstan-ignore-line [CODE_READONLY]
	public ?bool $httpOnly = null; //@phpstan-ignore-line [CODE_READONLY]
	/**
	 * @var string|null
	 * @phpstan-var globa-alias-cookie-same-site|null
	 */
	public ?string $sameSite = null; //@phpstan-ignore-line [CODE_READONLY]

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
