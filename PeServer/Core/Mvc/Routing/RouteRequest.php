<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Routing;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\RequestPath;

/**
 */
readonly class RouteRequest
{
	public function __construct(
		public HttpMethod $method,
		public RequestPath $path
	) {
	}
}
