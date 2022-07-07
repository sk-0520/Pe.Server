<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;


/**
 * ミドルウェアの入力パラメータ。
 */
class MiddlewareArgument
{
	public HttpResponse $response;

	/**
	 * 生成。
	 *
	 * @param RequestPath $requestPath
	 * @param SpecialStore $special
	 * @param CookieStore $cookie
	 * @param SessionStore $session
	 * @param HttpRequest $request
	 * @param ILogger $logger
	 */
	public function __construct(
		public RequestPath $requestPath,
		public SpecialStore $special,
		public CookieStore $cookie,
		public SessionStore $session,
		public HttpRequest $request,
		public ILogger $logger
	) {
		$this->response = new HttpResponse();
	}
}
