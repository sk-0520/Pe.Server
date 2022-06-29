<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\ILogger;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;

/**
 * コントローラ生成時に使用される入力値。
 */
class ControllerArgument
{
	/**
	 * 生成。
	 *
	 * @param CookieStore $cookie
	 * @param TemporaryStore $temporary
	 * @param SessionStore $session
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		/** @readonly */
		public CookieStore $cookie,
		/** @readonly */
		public TemporaryStore $temporary,
		/** @readonly */
		public SessionStore $session,
		/** @readonly */
		public ILogger $logger
	) {
	}
}
