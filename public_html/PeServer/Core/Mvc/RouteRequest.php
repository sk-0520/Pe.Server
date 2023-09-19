<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\Mvc\ControllerBase;
use PeServer\Core\Mvc\Middleware\IMiddleware;
use PeServer\Core\Mvc\Middleware\IShutdownMiddleware;

/**
 * @immutable
 */
class RouteRequest
{
	public function __construct(
		public HttpMethod $method,
		public RequestPath $path
	) {
	}
}
