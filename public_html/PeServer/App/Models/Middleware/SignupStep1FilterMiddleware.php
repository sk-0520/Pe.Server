<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\Core\Http\HttpStatus;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;


final class SignupStep1FilterMiddleware implements IMiddleware
{
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if (!SessionManager::existsAccount()) {
			return MiddlewareResult::none();
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
