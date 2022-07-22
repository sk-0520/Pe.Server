<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\SessionAccount;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

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
		if (!$argument->stores->session->tryGet(SessionManager::ACCOUNT, $account)) {
			return MiddlewareResult::error(HttpStatus::forbidden());
		}

		foreach ($levels as $level) {
			/** @var SessionAccount $account */
			if ($account->level === $level) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::forbidden());
	}

	protected abstract function filter(MiddlewareArgument $argument): MiddlewareResult;

	public final function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filter($argument);
	}

	public final function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
