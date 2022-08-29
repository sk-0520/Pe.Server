<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

/**
 * ある区間からの測定処理。
 *
 * グローバルに登録すれば全体、アクションに登録すればアクションの時間測定。
 * 別にそこまで厳密なものじゃないので `hrtime` は使用していない。
 */
final class PerformanceMiddleware implements IMiddleware
{
	#region variable

	private float $beforeMsec;

	#endregion

	public function __construct(
		private ILogger $logger
	) {
	}

	#region IMiddleware

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$this->beforeMsec = microtime(true);

		return MiddlewareResult::none();
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		$afterMsec = microtime(true);

		$this->logger->info('action {0} ms', $afterMsec - $this->beforeMsec);

		return MiddlewareResult::none();
	}

	#endregion
}
