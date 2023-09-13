<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\SessionKey;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;


final class NotLoginMiddleware implements IMiddleware
{
	public function __construct(
		private ILogger $logger
	) {
	}

	#region IMiddleware

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if ($argument->stores->session->tryGet(SessionKey::ACCOUNT, $account)) {
			$this->logger->warn('login user: {0}', $account);
			return MiddlewareResult::error(HttpStatus::notFound());
		}

		return MiddlewareResult::none();
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	#endregion
}
