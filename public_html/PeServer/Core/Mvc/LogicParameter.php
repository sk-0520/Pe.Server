<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Store\Stores;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;

/**
 * ロジック用パラメータ。
 * @immutable
 */
class LogicParameter
{
	/**
	 * 生成。
	 *
	 * @param HttpRequest $request リクエスト。
	 * @param Stores $stores
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		public HttpRequest $request,
		public Stores $stores,
		public ILogger $logger
	) {
	}
}
