<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpUrl;

readonly class HttpClientSecurityOptions
{
	/**
	 * 生成。
	 *
	 * @param bool $sslVerifyPeer
	 * @param bool $sslVerifyHost
	 * @param bool $urlAllowAuthentication
	 * @codeCoverageIgnore
	 */
	public function __construct(
		public bool $sslVerifyPeer = true,
		public bool $sslVerifyHost = true,
		public bool $urlAllowAuthentication = false,
	) {
	}
}
