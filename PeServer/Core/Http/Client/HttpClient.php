<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use Exception;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\DisposerBase;
use PeServer\Core\Http\Client\HttpClientOptions;
use PeServer\Core\Http\Client\HttpClientRequest;
use PeServer\Core\Http\Client\HttpClientResponse;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpClientRequestException;
use PeServer\Core\Throws\HttpClientStatusException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Web\Url;

/**
 * HTTPへ旅立つのだ。
 */
class HttpClient extends DisposerBase
{
	public function __construct(
		private HttpClientOptions $options
	) {
	}

	#region function

	public static function request(HttpClientRequest $request, HttpClientOptions $options): HttpClientResponse
	{
		$client = new self($options);
		return $client->send($request);
	}

	/**
	 * リクエストを内部用に変換。
	 *
	 * @param HttpClientRequest $raw
	 * @return HttpClientRequest
	 */
	protected function createRequest(HttpClientRequest $raw): HttpClientRequest
	{
		// HTTPヘッダをオプションから構築してユーザー設定で上書き
		$header = HttpHeader::createClientRequestHeader();
		$header->setValue('User-Agent', $this->options->userAgent);

		if ($raw->header !== null) {
			foreach ($raw->header->getHeaderNames() as $name) {
				$overwriteValues = $raw->header->getValues($name);
				$header->setValues($name, $overwriteValues);
			}
		}

		return new HttpClientRequest(
			$raw->url,
			$raw->method,
			$header,
			$raw->content
		);
	}

	protected function sendCore(HttpClientRequest $request): HttpClientResponse
	{
		$curlHandle = curl_init();

		/** @var array<int,mixed> */
		$curlOptions = [];

		//--------------------------------------------------------------
		// 共通
		$curlOptions[CURLOPT_URL] = $request->url->toString($this->options->urlEncoding); //cspell:disable-line
		$curlOptions[CURLOPT_RETURNTRANSFER] = true; //cspell:disable-line
		$curlOptions[CURLOPT_HEADER] = true; //cspell:disable-line
		$curlOptions[CURLOPT_CRLF] = false; //cspell:disable-line

		//--------------------------------------------------------------
		// リクエスト設定
		// メソッド
		/** @var HttpHeader|null */
		$overwriteHeader = null;
		//cspell:disable-next-line
		$curlOptions[CURLOPT_CUSTOMREQUEST] = $request->method->value;
		switch ($request->method) {
			case HttpMethod::Get:
			case HttpMethod::Head:
				break;

			case HttpMethod::Post:
			case HttpMethod::Put:
			case HttpMethod::Delete:
			case HttpMethod::Connect:
			case HttpMethod::Options:
			case HttpMethod::Trace:
			case HttpMethod::Patch:
				if ($request->content) {
					$overwriteHeader = HttpHeader::createClientRequestHeader();
					$customHeader = $request->content->toHeader();
					foreach ($customHeader->getHeaderNames() as $name) {
						$overwriteHeader->setValues($name, $customHeader->getValues($name));
					}
					//cspell:disable-next-line
					$curlOptions[CURLOPT_POSTFIELDS] = $request->content->toBody()->raw;
				} else {
					//cspell:disable-next-line
					$curlOptions[CURLOPT_POSTFIELDS] = Text::EMPTY;
				}
				break;

			default:
				throw new NotImplementedException();
		}

		// ヘッダ
		$headers = [];
		if ($request->header) {
			$headers = $request->header->getHeaders();

			if ($overwriteHeader) {
				$headers = Arr::replace($headers, $overwriteHeader->getHeaders());
			}
		} elseif ($overwriteHeader) {
			// 上で使ってる $request->header は差し替えられたものなので何かしらオブジェクトが入っている
			// なのでこっちに来ることはないと思うよ
			$headers = $overwriteHeader->getHeaders();
		}
		if (0 < Arr::getCount($headers)) {
			$headerList = [];
			foreach ($headers as $name => $values) {
				$headerList[] = "$name: $values";
			}
			//cspell:disable-next-line
			$curlOptions[CURLOPT_HTTPHEADER] = $headerList;
		}

		//--------------------------------------------------------------
		// オプション設定
		// リダイレクト
		$curlOptions[CURLOPT_FOLLOWLOCATION] = $this->options->redirect->isEnabled; //cspell:disable-line
		$curlOptions[CURLOPT_MAXREDIRS] = $this->options->redirect->count; //cspell:disable-line
		$curlOptions[CURLOPT_AUTOREFERER] = $this->options->redirect->autoReferer; //cspell:disable-line

		// セキュリティ
		$curlOptions[CURLOPT_DISALLOW_USERNAME_IN_URL] = !$this->options->security->urlAllowAuthentication; //cspell:disable-line
		$curlOptions[CURLOPT_SSL_VERIFYPEER] = $this->options->security->sslVerifyPeer; //cspell:disable-line
		$curlOptions[CURLOPT_SSL_VERIFYHOST] = $this->options->security->sslVerifyHost ? 2 : 0; //cspell:disable-line

		// プロキシ
		if ($this->options->proxy) {
			$curlOptions[CURLOPT_HTTPPROXYTUNNEL] = true; //cspell:disable-line
			$curlOptions[CURLOPT_PROXYPORT] = $this->options->proxy->port; //cspell:disable-line
			$curlOptions[CURLOPT_PROXY] = $this->options->proxy->host; //cspell:disable-line
			if (!Text::isNullOrEmpty($this->options->proxy->userName) || !Text::isNullOrEmpty($this->options->proxy->password)) {
				$curlOptions[CURLOPT_PROXYUSERPWD] = $this->options->proxy->userName . ':' . $this->options->proxy->password;
			}
		}

		curl_setopt_array($curlHandle, $curlOptions);

		/** @var string|false */
		$response = curl_exec($curlHandle);
		$clientStatus = HttpClientStatus::create($curlHandle);
		$information = HttpClientInformation::create($this->options->urlEncoding, $request, $curlHandle);
		$aaa = $information->getEffectiveUrl();

		/** @var HttpHeader|null */
		$responseHeader = null;
		/** @var Binary|null */
		$responseContent = null;

		if ($response === false) {
			$responseContent = new Binary(Text::EMPTY);
			$responseHeader = HttpHeader::getClientResponseHeader(new Binary(Text::EMPTY));
		} else {
			$rawResponse = new Binary($response);

			$headerSize = $information->getHeaderSize();
			$rawHeader = $rawResponse->getRange(0, $headerSize);
			$responseHeader = HttpHeader::getClientResponseHeader($rawHeader);
			$responseContent = $rawResponse->getRange($headerSize);
		}

		$result = new HttpClientResponse(
			$this->options,
			$request,
			$curlHandle,
			$responseHeader,
			$responseContent,
			$information,
			$clientStatus
		);


		if ($result->clientStatus->isError()) {
			throw new HttpClientStatusException($result);
		}

		$httpStatus = $result->information->getHttpStatus();
		if ($httpStatus->isError()) {
			throw new HttpClientRequestException($result);
		}

		return $result;
	}

	public function send(HttpClientRequest $request): HttpClientResponse
	{
		$request = $this->createRequest($request);
		return $this->sendCore($request);
	}

	final public function get(Url $url, ?HttpHeader $header = null): HttpClientResponse
	{
		$requestData = new HttpClientRequest($url, HttpMethod::Get, $header, null);
		$request = $this->createRequest($requestData);
		return $this->sendCore($request);
	}

	final public function post(Url $url, ?HttpHeader $header = null, ?HttpClientContentBase $content = null): HttpClientResponse
	{
		$requestData = new HttpClientRequest($url, HttpMethod::Post, $header, $content);
		$request = $this->createRequest($requestData);
		return $this->sendCore($request);
	}

	final public function put(Url $url, ?HttpHeader $header = null, ?HttpClientContentBase $content = null): HttpClientResponse
	{
		$requestData = new HttpClientRequest($url, HttpMethod::Put, $header, $content);
		$request = $this->createRequest($requestData);
		return $this->sendCore($request);
	}

	final public function patch(Url $url, ?HttpHeader $header = null, ?HttpClientContentBase $content = null): HttpClientResponse
	{
		$requestData = new HttpClientRequest($url, HttpMethod::Patch, $header, $content);
		$request = $this->createRequest($requestData);
		return $this->sendCore($request);
	}

	final public function delete(Url $url, ?HttpHeader $header = null, ?HttpClientContentBase $content = null): HttpClientResponse
	{
		$requestData = new HttpClientRequest($url, HttpMethod::Delete, $header, $content);
		$request = $this->createRequest($requestData);
		return $this->sendCore($request);
	}

	#endregion
}
