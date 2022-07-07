<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;

/**
 * コントローラ生成時に使用される入力値。
 * @immutable
 */
class ControllerArgument
{
	/**
	 * 生成。
	 *
	 * @param SpecialStore $special
	 * @param CookieStore $cookie
	 * @param TemporaryStore $temporary
	 * @param SessionStore $session
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		public SpecialStore $special,
		public CookieStore $cookie,
		public TemporaryStore $temporary,
		public SessionStore $session,
		public ILogger $logger
	) {
	}
}
