<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;


class StoreOption
{
	public CookieOption $cookie;
	public TemporaryOption $temporary;
	public SessionOption $session;

	public function __construct(CookieOption $cookie, TemporaryOption $temporary, SessionOption $session)
	{
		$this->cookie = $cookie;
		$this->temporary = $temporary;
		$this->session = $session;
	}
}
