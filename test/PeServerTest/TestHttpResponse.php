<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;

class TestHttpResponse
{
	public function __construct(
		public HttpResponse $response
	) {
	}

	#region

	public function getHttpStatus(): HttpStatus
	{
		return $this->response->status;
	}

	#endregion
}
