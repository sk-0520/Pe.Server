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
	 * ロガー
	 *
	 * @var ILogger
	 */
	public $logger;

	public CookieStore $cookie;
	public TemporaryStore $temporary;
	public SessionStore $session;

	public function __construct(CookieStore $cookie, TemporaryStore $temporary, SessionStore $session, ILogger $logger)
	{
		$this->cookie = $cookie;
		$this->temporary = $temporary;
		$this->session = $session;
		$this->logger = $logger;
	}
}
