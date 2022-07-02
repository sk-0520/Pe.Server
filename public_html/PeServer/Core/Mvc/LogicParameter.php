<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Log\ILogger;
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
	 * 生成。
	 *
	 * @param HttpRequest $request リクエスト。
	 * @param CookieStore $cookie
	 * @param TemporaryStore $temporary
	 * @param SessionStore $session
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		/** @readonly */
		public HttpRequest $request,
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
