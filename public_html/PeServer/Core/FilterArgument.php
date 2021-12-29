<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Store\SessionStore;

/**
 * フィルタリング時の入力パラメータ。
 */
class FilterArgument
{
	public CookieStore $cookie;
	public SessionStore $session;
	public ILogger $logger;

	public function __construct(CookieStore $cookie, SessionStore $session, ILogger $logger)
	{
		$this->cookie = $cookie;
		$this->session = $session;
		$this->logger = $logger;
	}
}
