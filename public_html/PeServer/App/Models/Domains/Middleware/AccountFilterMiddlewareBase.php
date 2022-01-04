<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\Core\HttpStatus;
use PeServer\Core\MiddlewareResult;
use PeServer\Core\MiddlewareArgument;
use PeServer\App\Models\SessionManager;
use PeServer\Core\IMiddleware;

abstract class AccountFilterMiddlewareBase implements IMiddleware
{
	/**
	 * アカウント用フィルタ処理。
	 *
	 * @param MiddlewareArgument $argument
	 * @param string[] $levels
	 * @return MiddlewareResult
	 */
	protected function filterCore(MiddlewareArgument $argument, array $levels): MiddlewareResult
	{
		if (!$argument->session->tryGet(SessionManager::ACCOUNT, $account)) {
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		foreach ($levels as $level) {
			if ($account['level'] === $level) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::forbidden());
	}

	protected abstract function filter(MiddlewareArgument $argument): MiddlewareResult;

	public final function handle(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filter($argument);
	}
}