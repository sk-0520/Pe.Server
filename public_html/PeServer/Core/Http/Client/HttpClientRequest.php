<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Http\Client\HttpClientContentBase;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Web\Url;

readonly class HttpClientRequest
{
	public function __construct(
		public Url $url,
		public HttpMethod $method,
		public ?HttpHeader $header,
		public ?HttpClientContentBase $content
	) {
	}
}
