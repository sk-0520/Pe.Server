<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Store\SessionStore;


/**
 * ロジック用パラメータ。
 */
class LogicParameter
{
	/**
	 * ロガー
	 *
	 * @var ILogger
	 */
	public $logger;
	/**
	 * リクエスト。
	 *
	 * @var ActionRequest
	 */
	public $request;

	public CookieStore $cookie;
	public SessionStore $session;

	public function __construct(ActionRequest $request, CookieStore $cookie, SessionStore $session, ILogger $logger)
	{
		$this->request = $request;
		$this->cookie = $cookie;
		$this->session = $session;
		$this->logger = $logger;
	}
}
