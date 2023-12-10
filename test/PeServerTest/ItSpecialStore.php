<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;

final class ItSpecialStore extends SpecialStore
{
	public function __construct(
		private HttpMethod $httpMethod,
		private string $requestUri,
		private ?HttpHeader $httpHeader,
		private ?ItBody $body,
	) {
	}

	#region SpecialStore

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

	public function containsGetName(string $name): bool
	{
		return $this->containsMethodName($name);
	}

	public function getGet(string $name, string $fallbackValue = Text::EMPTY): string
	{
		return $this->getMethod($name, $fallbackValue);
	}

	public function tryGetGet(string $name, ?string &$result): bool
	{
		return $this->tryMethodGet($name, $result);
	}

	public function getGetNames(): array
	{
		return $this->getMethodNames();
	}

	public function containsPostName(string $name): bool
	{
		return $this->containsMethodName($name);
	}

	public function getPost(string $name, string $fallbackValue = Text::EMPTY): string
	{
		return $this->getMethod($name, $fallbackValue);
	}

	public function tryPostGet(string $name, ?string &$result): bool
	{
		return $this->tryMethodGet($name, $result);
	}

	public function getPostNames(): array
	{
		return $this->getMethodNames();
	}

	#endregion

	#region function

	private function containsMethodName(string $name): bool
	{
		if (!$this->body) {
			return false;
		}

		assert($this->body->content instanceof Dictionary);
		return isset($this->body->content[$name]);
	}

	private function getMethod(string $name, string $fallbackValue = Text::EMPTY): string
	{
		if (!$this->body) {
			return $fallbackValue;
		}

		assert($this->body->content instanceof Dictionary);
		$result = Arr::getOr($this->body->content->getArray(), $name, $fallbackValue);
		return $result;
	}

	private function tryMethodGet(string $name, ?string &$result): bool
	{
		if (!$this->body) {
			return false;
		}

		assert($this->body->content instanceof Dictionary);
		return Arr::tryGet($this->body->content->getArray(), $name, $result);
	}

	private function getMethodNames(): array
	{
		assert($this->body->content instanceof Dictionary);
		return Arr::getKeys($this->body->content->getArray());
	}

	#endregion
}
