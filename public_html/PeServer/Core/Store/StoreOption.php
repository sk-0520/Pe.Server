<?php

declare(strict_types=1);

namespace PeServer\Core\Store;


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
