<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\App\Models\Domains\UserLevel;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Domains\Middleware\AccountFilterMiddlewareBase;

final class SetupAccountFilterMiddleware extends AccountFilterMiddlewareBase
{
	protected function filter(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filterCore($argument, [UserLevel::SETUP]);
	}
}
