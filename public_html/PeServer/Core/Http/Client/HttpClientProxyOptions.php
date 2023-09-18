<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Text;

/**
 * HttpClient プロキシ設定データ。
 */
readonly class HttpClientProxyOptions
{
	public function __construct(
		public string $host,
		public int $port,
		public string $userName = Text::EMPTY,
		public string $password = Text::EMPTY
	) {
	}
}
