<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;

/**
 * クッキーとかあれこれ一覧。
 *
 * @immutable
 */
class Stores
{
	/** Cookie */
	public CookieStore $cookie;
	/** セッション */
	public SessionStore $session;
	/** 一時 */
	public TemporaryStore $temporary;

	/**
	 * 生成。
	 *
	 * @param SpecialStore $special
	 * @param StoreOptions $options
	 */
	public function __construct(
		public SpecialStore $special,
		private StoreOptions $options
	) {
		$this->special = $this->special;
		$this->cookie = new CookieStore($this->special, $this->options->cookie);
		$this->temporary = new TemporaryStore($this->options->temporary, $this->cookie);
		$this->session = new SessionStore($this->options->session, $this->cookie);
	}

	public function apply(): void
	{
		$this->session->apply();
		$this->temporary->apply();
		$this->cookie->apply();
	}
}
