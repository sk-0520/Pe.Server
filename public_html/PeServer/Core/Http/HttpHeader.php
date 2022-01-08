<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * HTTPヘッダー
 *
 * TODO: 構築中。
 */
class HttpHeader
{
	/**
	 * Undocumented variable
	 *
	 * @var array<string,string[]>
	 */
	private array $headers = [];

	/**
	 * Undocumented variable
	 *
	 * @var array{url:string,status?:HttpStatus}|null
	 */
	private ?array $redirect = null;

	private function throwIfInvalidHeaderName(string $name): void
	{
		if (StringUtility::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}
		if (StringUtility::toLower($name) === 'location') {
			throw new ArgumentException('$name: setRedirect()');
		}
	}

	public function setValue(string $name, string $value): void
	{
		$this->throwIfInvalidHeaderName($name);

		$this->headers[$name] = [$value];
	}

	/**
	 * Undocumented function
	 *
	 * @param string $name
	 * @param string[] $values
	 * @return void
	 */
	public function setValues(string $name, array $values): void
	{
		$this->throwIfInvalidHeaderName($name);

		$this->headers[$name] = $values;
	}


	public function addValue(string $name, string $value): void
	{
		$this->throwIfInvalidHeaderName($name);

		if (ArrayUtility::tryGet($this->headers, $name, $result)) {
			$result[] = $value;
			$this->headers[$name] = $result;
		} else {
			$this->headers[$name] = [$value];
		}
	}

	public function existsHeader(string $name): bool
	{
		return ArrayUtility::existsKey($this->headers, $name);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $name
	 * @return string[]
	 */
	public function getValues(string $name): array
	{
		if (ArrayUtility::tryGet($this->headers, $name, $result)) {
			return $result;
		}

		throw new KeyNotFoundException();
	}

	public function existsRedirect(): bool
	{
		return !is_null($this->redirect);
	}

	public function setRedirect(string $url, ?HttpStatus $status): void
	{
		if (is_null($status)) {
			$this->redirect = [
				'url' => $url,
			];
		} else {
			$this->redirect = [
				'url' => $url,
				'status' => $status,
			];
		}
	}

	public function clearRedirect(): bool
	{
		if (is_null($this->redirect)) {
			return false;
		}
		$this->redirect = null;

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @return array<string,string>
	 */
	public function getHeaders(): array
	{
		/** @var array<string,string> */
		$joinHeaders = [];

		foreach ($this->headers as $name => $values) {
			$joinHeaders[$name] = StringUtility::join($values, ', ');
		}

		return $joinHeaders;
	}

	/**
	 * Undocumented function
	 *
	 * @return array{url:string,status?:HttpStatus}
	 */
	public function getRedirect(): array
	{
		if (!$this->existsRedirect()) {
			throw new InvalidOperationException();
		}

		return $this->redirect; //@phpstan-ignore-line not null
	}
}
