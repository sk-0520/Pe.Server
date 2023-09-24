<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\Data\SessionAccount;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpResponse;
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
		if (!$argument->stores->session->tryGet(SessionKey::ACCOUNT, $account)) {
			return MiddlewareResult::error(HttpStatus::Forbidden);
		}

		foreach ($levels as $level) {
			/** @var SessionAccount $account */
			if ($account->level === $level) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::Forbidden);
	}

	abstract protected function filter(MiddlewareArgument $argument): MiddlewareResult;

	//[IMiddleware]

	final public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filter($argument);
	}

	final public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
