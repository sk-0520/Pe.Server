<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Middleware;

use PeServer\Core\MiddlewareArgument;
use PeServer\App\Models\Domains\Middleware\AccountFilterMiddlewareBase;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\Core\MiddlewareResult;

final class SetupAccountFilterMiddleware extends AccountFilterMiddlewareBase
{
	protected function filter(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filterCore($argument, [UserLevel::SETUP]);
	}
}
