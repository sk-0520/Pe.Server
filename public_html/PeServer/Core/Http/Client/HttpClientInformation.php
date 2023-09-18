<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use CurlHandle;
use PeServer\Core\Http\Client\HttpClientRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Web\Url;
use PeServer\Core\Web\UrlEncoding;

/** */
class HttpClientInformation
{
	/**
	 * 生成。
	 *
	 * @param UrlEncoding $urlEncoding
	 * @param HttpClientRequest $request
	 * @param array<string,mixed> $rawItems
	 */
	public function __construct(
		readonly private UrlEncoding $urlEncoding,
		readonly private HttpClientRequest $request, //@phpstan-ignore-line
		readonly public array $rawItems
	) {
	}

	#region

	public static function create(UrlEncoding $urlEncoding, HttpClientRequest $request, CurlHandle $curlHandle): self
	{
		$items = curl_getinfo($curlHandle);

		return new self(
			$urlEncoding,
			$request,
			$items
		);
	}

	public function getHttpStatus(): HttpStatus
	{
		return HttpStatus::from($this->rawItems['http_code']);
	}

	public function getEffectiveUrl(): Url
	{
		return Url::parse($this->rawItems['url'], $this->urlEncoding);
	}

	public function getHeaderSize(): int
	{
		return $this->rawItems['header_size'];
	}

	public function getRedirectCount(): int
	{
		return $this->rawItems['redirect_count'];
	}

	#endregion
}
