<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateInterval;

class CookieOption
{
	public string $path;
	public ?DateInterval $span;
	public bool $secure;
	public bool $httpOnly;

	public function __construct(string $path, ?DateInterval $span, bool $secure, bool $httpOnly)
	{
		$this->path = $path;
		$this->span = $span;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

	public function getTotalMinutes(): int
	{
		if (is_null($this->span)) {
			return 0;
		}

		return ($this->span->d * 24 * 60) + ($this->span->h * 60) + $this->span->i;
	}
}
