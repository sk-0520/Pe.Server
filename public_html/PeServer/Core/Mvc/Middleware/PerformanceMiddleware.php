<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware;

/**
 * ある区間からの測定処理。
 *
 * グローバルに登録すれば全体、アクションに登録すればアクションの時間測定。
 * 別にそこまで厳密なものじゃないので `hrtime` は使用していない。
 */
class PerformanceMiddleware implements IMiddleware
{
	private float $beforeMsec;

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		$this->beforeMsec = microtime(true);

		return MiddlewareResult::none();
	}

	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		$afterMsec = microtime(true);

		$argument->logger->info('action {0} ms', $afterMsec - $this->beforeMsec);

		return MiddlewareResult::none();
	}
}
