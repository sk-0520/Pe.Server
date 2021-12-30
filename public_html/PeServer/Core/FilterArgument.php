<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\Mvc\ActionRequest;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Store\SessionStore;

/**
 * フィルタリング時の入力パラメータ。
 */
class FilterArgument
{
	public CookieStore $cookie;
	public SessionStore $session;
	public ActionRequest $request;
	public ILogger $logger;

	public function __construct(CookieStore $cookie, SessionStore $session, ActionRequest $request, ILogger $logger)
	{
		$this->cookie = $cookie;
		$this->session = $session;
		$this->request = $request;
		$this->logger = $logger;
	}
}
