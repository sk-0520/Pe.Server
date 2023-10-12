<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\CaseInsensitiveKeyArray;
use PeServer\Core\Encoding;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\RedirectSetting;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Web\Url;

/**
 * HTTPヘッダー。
 */
class HttpHeader
{
	#region variable

	/**
	 * ヘッダ一覧。
	 *
	 * リダイレクト(Location)とは共存しない。
	 *
	 * @var CaseInsensitiveKeyArray
	 * @phpstan-var CaseInsensitiveKeyArray<non-empty-string,string[]>
	 */
	private CaseInsensitiveKeyArray $headers;

	/**
	 * リダイレクト設定。
	 *
	 * @var RedirectSetting|null
	 */
	private ?RedirectSetting $redirect = null;

	#endregion

	public function __construct()
	{
		$this->headers = new CaseInsensitiveKeyArray();
	}

	#region function

	/**
	 * HTTPヘッダ名が不正であれば例外を投げる。
	 *
	 * @param string $name ヘッダ名
	 * @throws ArgumentException HTTPヘッダ名不正。
	 */
	protected function throwIfInvalidHeaderName(string $name): void
	{
		if (Text::isNullOrWhiteSpace($name)) {
			throw new ArgumentException('$name');
		}
		if (Text::toLower($name) === 'location') {
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

		if (isset($this->headers[$name])) {
			$array = $this->headers[$name];
			$array[] = $value;
			$this->headers[$name] = $array;
			return;
		}

		$this->headers[$name] = [$value];
	}

	/**
	 * ヘッダ名が存在するか。
	 *
	 * @param string $name ヘッダ名。
	 * @phpstan-param non-empty-string $name
	 */
	public function existsHeader(string $name): bool
	{
		return isset($this->headers[$name]);
	}

	public function existsContentType(): bool
	{
		return $this->existsHeader(ContentType::NAME);
	}

	public function getContentType(): ContentType
	{
		$contentType = $this->getValues(ContentType::NAME);
		return ContentType::from($contentType);
	}

	public function setContentType(ContentType $value): void
	{
		$this->setValues(ContentType::NAME, $value->toValues());
	}

	/**
	 * ヘッダ名一覧を取得。
	 *
	 * @return string[]
	 * @phpstan-return non-empty-string[]
	 */
	public function getHeaderNames(): array
	{
		$result = [];

		foreach ($this->headers as $name => $_) {
			$result[] = $name;
		}

		return $result;
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
		if (isset($this->headers[$name])) {
			return $this->headers[$name];
		}

		throw new KeyNotFoundException();
	}

	/**
	 * ヘッダの削除。
	 *
	 * @param string $name ヘッダ名。
	 * @phpstan-param non-empty-string $name
	 * @return bool 削除できたか。
	 */
	public function clearHeader(string $name): bool
	{
		$this->throwIfInvalidHeaderName($name);

		if (!isset($this->headers[$name])) {
			return false;
		}

		unset($this->headers[$name]);

		return true;
	}

	/**
	 * リダイレクト設定は存在するか。
	 */
	public function existsRedirect(): bool
	{
		return $this->redirect !== null;
	}

	/**
	 * リダイレクト設定を割り当て。
	 *
	 * @param Url $url
	 * @param HttpStatus|null $status
	 * @return void
	 */
	public function setRedirect(Url $url, ?HttpStatus $status): void
	{
		if ($status === null) {
			$this->redirect = new RedirectSetting($url, HttpStatus::MovedPermanently);
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
		if ($this->redirect === null) {
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
			$value = Text::join(', ', $values);
			if (0 < Text::getLength($value)) {
				$joinHeaders[$name] = $value;
			}
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
	 * HTTPクライアントリクエストヘッダの生成。
	 *
	 * @return HttpHeader
	 */
	public static function createClientRequestHeader(): HttpHeader
	{
		return new LocalHttpClientRequestHeader();
	}

	public static function getClientResponseHeader(Binary $responseHeader, ?Encoding $encoding = null): HttpHeader
	{
		$encoding ??= Encoding::getDefaultEncoding();
		return new LocalClientResponseHttpHeader($responseHeader, $encoding);
	}

	/**
	 * リクエストヘッダの取得。
	 *
	 * @return HttpHeader
	 */
	public static function getRequestHeader(): HttpHeader
	{
		return new LocalHttpRequestHeader();
	}

	#endregion
}

/**
 * 要求時のヘッダー一覧。
 */
//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalHttpClientRequestHeader extends HttpHeader
{
	public function existsRedirect(): bool
	{
		throw new NotSupportedException();
	}

	public function setRedirect(Url $url, ?HttpStatus $status): void
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


//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalClientResponseHttpHeader extends LocalHttpClientRequestHeader
{
	private bool $initialized = false;

	public function __construct(Binary $responseHeader, Encoding $encoding)
	{
		parent::__construct();

		$headerString = $encoding->toString($responseHeader);
		$headers = Text::splitLines($headerString);
		foreach ($headers as $index => $header) {
			if ($index === 0) {
				// HTTP仕様の最初のあれなんでいらない
				continue;
			}
			if ($header === '') {
				// 改行分割後に何もないのであればHTTPの仕様に従って本文開始になるのでもう何もしない
				break;
			}

			$kv = Text::split($header, ':', 2);
			$name = Text::trim($kv[0]);
			if (!Text::isNullOrWhiteSpace($name)) {
				$this->setValue($name, isset($kv[1]) ? Text::trim($kv[1]) : Text::EMPTY);
			}
		}
	}

	protected function throwIfInvalidHeaderName(string $name): void
	{
		if ($this->initialized) {
			throw new NotSupportedException();
		}
	}
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalHttpRequestHeader extends LocalHttpClientRequestHeader
{
	public function __construct()
	{
		parent::__construct();

		$headers = getallheaders();
		foreach ($headers as $name => $value) {
			//@phpstan-ignore-next-line non-empty
			$this->setValue($name, $value);
		}
	}

	protected function throwIfInvalidHeaderName(string $name): void
	{
		//NOP
	}
}
