<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use \Throwable;
use PeServer\Core\Http\Client\HttpClientResponse;
use PeServer\Core\Throws\CoreException;

final class HttpClientRequestException extends CoreException
{
	public function __construct(public readonly HttpClientResponse $response, ?Throwable $previous = null)
	{
		parent::__construct($response->clientStatus->message, $response->clientStatus->number, $previous);
	}
}
