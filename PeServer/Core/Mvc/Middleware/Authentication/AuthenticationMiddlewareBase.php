<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Middleware\Authentication;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

abstract class AuthenticationMiddlewareBase implements IMiddleware
{
	public function __construct(
		protected ILogger $logger
	) {
		//NOP
	}

	#region function

	abstract protected function filter(MiddlewareArgument $argument): MiddlewareResult;

	#endregion

	#region IMiddleware

	final public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return $this->filter($argument);
	}

	public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	#endregion
}
