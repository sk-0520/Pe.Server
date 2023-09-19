<?php

declare(strict_types=1);

namespace PeServer\App\Models\Middleware\Api;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\MiddlewareArgument;
use PeServer\Core\Mvc\Middleware\MiddlewareResult;

class ApiAcaoMiddleware implements IMiddleware
{
	public function __construct()
    {
	}

	#region IMiddleware

	public function handleBefore(MiddlewareArgument $argument): MiddlewareResult
	{
		return MiddlewareResult::none();
	}

	final public function handleAfter(MiddlewareArgument $argument, HttpResponse $response): MiddlewareResult
	{
		$response->header->addValue('Access-Control-Allow-Origin', '*');
		return MiddlewareResult::none();
	}

	#endregion
}
