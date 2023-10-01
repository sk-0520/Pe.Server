<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;

final class TestDynamicSpecialStore extends SpecialStore
{
	public function __construct(
		private HttpMethod $httpMethod,
		private string $requestUri,
		private ?HttpHeader $httpHeader,
		private ?array $body,
	) {
	}

	public function getServer(string $name, mixed $fallbackValue = Text::EMPTY): mixed
	{
		switch ($name) {
			case 'REQUEST_METHOD':
				return $this->httpMethod->value;

			case 'REQUEST_URI':
				return $this->requestUri;

			default:
				break;
		}

		return parent::getServer($name, $fallbackValue);
	}

	public function getRequestHeader(): HttpHeader
	{
		return $this->httpHeader ?? new HttpHeader();
	}
}
