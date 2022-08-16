<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware;

use PeServer\App\Models\SessionKey;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;


final class SignupStep1FilterMiddleware implements IMiddleware
{
	//[IMiddleware]

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		if (!$argument->stores->session->tryGet(SessionKey::ACCOUNT, $unused)) {
			return MiddlewareResult::none();
		}

		return MiddlewareResult::error(HttpStatus::notFound());
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}
}
