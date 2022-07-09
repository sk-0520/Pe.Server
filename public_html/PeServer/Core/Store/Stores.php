<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;

class Stores
{
	public CookieStore $cookie;
	public SessionStore $session;
	public TemporaryStore $temporary;

	public function __construct(
		public SpecialStore $special,
		private StoreOptions $options
	) {
		$this->special = $this->special;
		$this->cookie = new CookieStore($this->special, $this->options->cookie);
		$this->temporary = new TemporaryStore($this->options->temporary, $this->cookie);
		$this->session = new SessionStore($this->options->session, $this->cookie);
	}
}
