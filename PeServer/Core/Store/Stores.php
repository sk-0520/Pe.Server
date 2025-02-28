<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Mvc\Logic\ILogicFactory;
use PeServer\Core\Web\WebSecurity;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;

/**
 * クッキーとかあれこれ一覧。
 */
readonly class Stores
{
	#region variable

	/** Cookie */
	public CookieStore $cookie;
	/** セッション */
	public SessionStore $session;
	/** 一時 */
	public TemporaryStore $temporary;

	#endregion

	/**
	 * 生成。
	 *
	 * @param SpecialStore $special
	 * @param StoreOptions $options
	 */
	public function __construct(
		public SpecialStore $special,
		private StoreOptions $options,
		WebSecurity $webSecurity
	) {
		$this->cookie = new CookieStore($this->special, $this->options->cookie);
		$this->temporary = new TemporaryStore($this->options->temporary, $this->cookie);
		$this->session = new SessionStore($this->options->session, $this->cookie, $webSecurity);
	}

	#region function

	public function apply(): void
	{
		$this->session->apply();
		$this->temporary->apply();
		$this->cookie->apply();
	}

	#endregion
}
