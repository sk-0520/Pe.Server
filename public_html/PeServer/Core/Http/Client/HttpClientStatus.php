<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use CurlHandle;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;

readonly class HttpClientStatus
{
	public function __construct(
		public int $number,
		public string $message
	) {
	}

	#region function

	public static function create(CurlHandle $curlHandle): self
	{
		return new self(
			curl_errno($curlHandle),
			curl_error($curlHandle)
		);
	}

	public function isError(): bool
	{
		return $this->number !== 0;
	}

	#endregion
}
