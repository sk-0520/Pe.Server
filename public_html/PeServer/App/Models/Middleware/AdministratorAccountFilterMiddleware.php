<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\Domain\UserLevel;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\App\Models\Middleware\AccountFilterMiddlewareBase;

final class AdministratorAccountFilterMiddleware extends AccountFilterMiddlewareBase
{
	//[AccountFilterMiddlewareBase]

	protected function filter(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filterCore($argument, [UserLevel::ADMINISTRATOR]);
	}
}
