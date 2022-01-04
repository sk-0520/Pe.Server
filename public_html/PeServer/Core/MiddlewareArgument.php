<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Mvc\ActionRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;

/**
 * ミドルウェアの入力パラメータ。
 */
class MiddlewareArgument
{
	public bool $isBefore;
	public RequestPath $requestPath;
	public CookieStore $cookie;
	public SessionStore $session;
	public ActionRequest $request;
	public ILogger $logger;

	/**
	 * 生成。
	 *
	 * @param bool $isBefore
	 * @param RequestPath $requestPath
	 * @param CookieStore $cookie
	 * @param SessionStore $session
	 * @param ActionRequest $request
	 * @param ILogger $logger
	 */
	public function __construct(bool $isBefore, RequestPath $requestPath, CookieStore $cookie, SessionStore $session, ActionRequest $request, ILogger $logger)
	{
		$this->isBefore = $isBefore;
		$this->requestPath = $requestPath;
		$this->cookie = $cookie;
		$this->session = $session;
		$this->request = $request;
		$this->logger = $logger;
	}
}
