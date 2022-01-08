<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\HttpRequest;
use PeServer\App\Models\AppDatabase;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Dao\Entities\SignUpWaitEmailsEntityDao;
use PeServer\App\Models\SessionManager;

final class SignupStep1FilterMiddleware implements IMiddleware
{
	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if (!SessionManager::isEnabled()) {
			if (!SessionManager::existsAccount()) {
				return MiddlewareResult::none();
			}
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
