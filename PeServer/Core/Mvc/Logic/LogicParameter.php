<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Logic;

use PeServer\Core\Environment;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Store\Stores;
use PeServer\Core\Http\HttpRequest;

/**
 * ロジック用パラメータ。
 */
readonly class LogicParameter
{
	/**
	 * 生成。
	 *
	 * @param HttpRequest $request リクエスト。
	 * @param Stores $stores
	 * @param Environment $environment
	 * @param ILogger $logger ロガー。
	 */
	public function __construct(
		public HttpRequest $request,
		public Stores $stores,
		public Environment $environment,
		public ILogger $logger
	) {
	}
}
