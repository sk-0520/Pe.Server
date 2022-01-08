<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\ILogger;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;

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
	 * @var HttpRequest
	 */
	public $request;

	public CookieStore $cookie;
	public TemporaryStore $temporary;
	public SessionStore $session;

	public function __construct(HttpRequest $request, CookieStore $cookie, TemporaryStore $temporary, SessionStore $session, ILogger $logger)
	{
		$this->request = $request;
		$this->cookie = $cookie;
		$this->temporary = $temporary;
		$this->session = $session;
		$this->logger = $logger;
	}
}
