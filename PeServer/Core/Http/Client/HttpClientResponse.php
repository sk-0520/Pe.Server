<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use CurlHandle;
use PeServer\Core\Binary;
use PeServer\Core\DisposerBase;
use PeServer\Core\Http\Client\HttpClientOptions;
use PeServer\Core\Http\Client\HttpClientRequest;
use PeServer\Core\Http\Client\HttpClientStatus;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Throws\HttpClientException;

/** */
class HttpClientResponse extends DisposerBase
{
	/**
	 * 生成。
	 *
	 * @param HttpClientOptions $options
	 * @param HttpClientRequest $request
	 * @param CurlHandle $curlHandle
	 * @param HttpHeader $header
	 * @param Binary $content
	 * @param HttpClientInformation $information
	 * @param HttpClientStatus $clientStatus
	 */
	public function __construct(
		readonly public HttpClientOptions $options,
		readonly public HttpClientRequest $request,
		private CurlHandle $curlHandle,
		readonly public HttpHeader $header,
		readonly public Binary $content,
		readonly public HttpClientInformation $information,
		readonly public HttpClientStatus $clientStatus
	) {
	}

	#region function


	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		curl_close($this->curlHandle);
	}

	#endregion
}
