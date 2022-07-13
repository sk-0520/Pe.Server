<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;

/**
 * HTTPヘッダー。
 */
class HttpHeader
{
	/**
	 * ヘッダ一覧。
	 *
	 * リダイレクト(Location)とは共存しない。
	 *
	 * @var array<non-empty-string,string[]>
	 */
	private array $headers = [];

	/**
	 * リダイレクト設定。
	 *
	 * @var RedirectSetting|null
	 */
	private ?RedirectSetting $redirect = null;

	/**
	 * HTTPヘッダ名が不正であれば例外を投げる。
	 *
	 * @param string $name ヘッダ名
	 * @phpstan-param non-empty-string $name
	 * @throws ArgumentException HTTPヘッダ名不正。
	 */
	private function throwIfInvalidHeaderName(string $name): void
	{
		if (StringUtility::isNullOrWhiteSpace($name)) { //@phpstan-ignore-line non-empty
			throw new ArgumentException('$name');
		}
		if (StringUtility::toLower($name) === 'location') {
			throw new ArgumentException('$name: setRedirect()');
		}
	}

	/**
	 * HTTPヘッダ設定
	 *
	 * @param string $name ヘッダ名。
	 * @phpstan-param non-empty-string $name
	 * @param string $value 値。
	 * @throws ArgumentException ヘッダ名不正。
	 */
	public function setValue(string $name, string $value): void
	{
		$this->throwIfInvalidHeaderName($name);

		$this->headers[$name] = [$value];
	}

	/**
	 * ヘッダに値一覧を設定。
	 *
	 * @param string $name ヘッダ名。
	 * @phpstan-param non-empty-string $name
	 * @param string[] $values 値一覧。
	 * @throws ArgumentException ヘッダ名不正。
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
	 * @phpstan-param non-empty-string $name
	 * @param string $value 値。
	 * @throws ArgumentException HTTPヘッダ名不正。
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
	 * @param string $name ヘッダ名。
	 */
	public function existsHeader(string $name): bool
	{
		return ArrayUtility::existsKey($this->headers, $name);
	}

	/**
	 * ヘッダの値を取得。
	 *
	 * @param string $name ヘッダ名。
	 * @phpstan-param non-empty-string $name
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
			$this->redirect = new RedirectSetting($url, HttpStatus::moved());
		} else {
			$this->redirect = new RedirectSetting($url, $status);
		}
	}

	/**
	 * リダイレクト設定を破棄。
	 *
	 * @return boolean 破棄したか。
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
	 * @return RedirectSetting
	 * @throws InvalidOperationException リダイレクト設定が未割り当て。
	 */
	public function getRedirect(): RedirectSetting
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
		return new LocalRequestHttpHeader();
	}
}

/**
 * 要求時のヘッダー一覧。
 */
final class LocalRequestHttpHeader extends HttpHeader
{
	public function __construct()
	{
		$headers = getallheaders();
		foreach ($headers as $name => $value) {
			//@phpstan-ignore-next-line non-empty
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

	public function getRedirect(): RedirectSetting
	{
		throw new NotSupportedException();
	}
}
