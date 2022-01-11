<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * HTTPヘッダー
 *
 * TODO: 構築中。
 */
class HttpHeader
{
	/**
	 * ヘッダ一覧。
	 *
	 * リダイレクト(Location)とは共存しない。
	 *
	 * @var array<string,string[]>
	 */
	private array $headers = [];

	/**
	 * リダイレクト設定。
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
	 * ヘッダに値一覧を設定。
	 *
	 * @param string $name ヘッダ名。
	 * @param string[] $values 値一覧。
	 * @return void
	 */
	public function setValues(string $name, array $values): void
	{
		$this->throwIfInvalidHeaderName($name);

		$this->headers[$name] = $values;
	}

	/**
	 * ヘッダに値を追加。
	 *
	 * @param string $name ヘッダ名。
	 * @param string $value 値。
	 * @return void
	 */
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

	/**
	 * ヘッダ名が存在するか。
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function existsHeader(string $name): bool
	{
		return ArrayUtility::existsKey($this->headers, $name);
	}

	/**
	 * ヘッダの値を取得。
	 *
	 * @param string $name
	 * @return string[] 値一覧。
	 * @throws KeyNotFoundException
	 */
	public function getValues(string $name): array
	{
		if (ArrayUtility::tryGet($this->headers, $name, $result)) {
			return $result;
		}

		throw new KeyNotFoundException();
	}

	/**
	 * リダイレクト設定は存在するか。
	 *
	 * @return boolean
	 */
	public function existsRedirect(): bool
	{
		return !is_null($this->redirect);
	}

	/**
	 * リダイレクト設定を割り当て。
	 *
	 * @param string $url
	 * @param HttpStatus|null $status
	 * @return void
	 */
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

	/**
	 * リダイレクト設定を破棄。
	 *
	 * @return boolean
	 */
	public function clearRedirect(): bool
	{
		if (is_null($this->redirect)) {
			return false;
		}
		$this->redirect = null;

		return true;
	}

	/**
	 * 現在のヘッダ一覧を取得。
	 *
	 * 同一名ヘッダは , でまとめられる。
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
	 * リダイレクト情報を取得。
	 *
	 * @return array{url:string,status?:HttpStatus}
	 * @throws InvalidOperationException リダイレクト設定が未割り当て。
	 */
	public function getRedirect(): array
	{
		if (!$this->existsRedirect()) {
			throw new InvalidOperationException();
		}

		return $this->redirect; //@phpstan-ignore-line not null
	}

	/**
	 * リクエストヘッダの取得。
	 *
	 * @return HttpHeader
	 */
	public static function getRequest(): HttpHeader
	{
		return new _HttpHeader_Request();
	}
}

/**
 * 要求時のヘッダー一覧。
 */
final class _HttpHeader_Request extends HttpHeader
{
	public function __construct()
	{
		$headers = getallheaders();
		foreach ($headers as $name => $value) {
			$this->setValue($name, $value);
		}
	}

	public function existsRedirect(): bool
	{
		throw new NotSupportedException();
	}

	public function setRedirect(string $url, ?HttpStatus $status): void
	{
		throw new NotSupportedException();
	}

	public function clearRedirect(): bool
	{
		throw new NotSupportedException();
	}

	public function getRedirect(): array
	{
		throw new NotSupportedException();
	}
}
