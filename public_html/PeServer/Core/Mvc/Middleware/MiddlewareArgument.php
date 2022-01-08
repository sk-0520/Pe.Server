<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\ILogger;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;

/**
 * ミドルウェアの入力パラメータ。
 */
class MiddlewareArgument
{
	public RequestPath $requestPath;
	public CookieStore $cookie;
	public SessionStore $session;
	public HttpRequest $request;
	public HttpResponse $response;
	public ILogger $logger;

	/**
	 * 生成。
	 *
	 * @param RequestPath $requestPath
	 * @param CookieStore $cookie
	 * @param SessionStore $session
	 * @param HttpRequest $request
	 * @param ILogger $logger
	 */
	public function __construct(RequestPath $requestPath, CookieStore $cookie, SessionStore $session, HttpRequest $request, ILogger $logger)
	{
		$this->requestPath = $requestPath;
		$this->cookie = $cookie;
		$this->session = $session;
		$this->request = $request;
		$this->response = new HttpResponse();
		$this->logger = $logger;
	}
}
