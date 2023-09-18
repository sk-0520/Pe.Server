<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Http\Client\HttpClientProxyOptions;
use PeServer\Core\Http\Client\HttpClientSecurityOptions;
use PeServer\Core\Http\Client\HttpRedirectOptions;
use PeServer\Core\Text;
use PeServer\Core\Web\UrlEncoding;

/**
 * HttpClient 設定データ。
 */
readonly class HttpClientOptions
{
	#region variable

	public UrlEncoding $urlEncoding;

	#endregion

	public function __construct(
		public string $userAgent = Text::EMPTY,
		public HttpRedirectOptions $redirect = new HttpRedirectOptions(),
		public HttpClientSecurityOptions $security = new HttpClientSecurityOptions(),
		public ?HttpClientProxyOptions $proxy = null,
		?UrlEncoding $urlEncoding = null
	) {
		$this->urlEncoding = $urlEncoding ?? UrlEncoding::createDefault();
	}
}
