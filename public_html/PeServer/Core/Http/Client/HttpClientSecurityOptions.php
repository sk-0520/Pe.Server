<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpUrl;

readonly class HttpClientSecurityOptions
{
	public function __construct(
		public bool $sslVerifyPeer = true,
		public bool $sslVerifyHost = true,
		public bool $urlAllowAuthentication = false,
	) {
	}
}
