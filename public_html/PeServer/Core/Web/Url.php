<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use Stringable;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collections;
use PeServer\Core\Encoding;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ParseException;
use PeServer\Core\Web\UrlEncoding;
use PeServer\Core\Web\UrlPath;
use PeServer\Core\Web\UrlQuery;

/**
 * URL。
 */

readonly class Url implements Stringable
{
	/**
	 * 生成。
	 *
	 * 基本的には `tryParse`/`parse` を使用する想定。
	 *
	 * @param non-empty-string $scheme
	 * @param string $user
	 * @param string $password
	 * @param non-empty-string $host
	 * @param int|null $port `null` は未指定
	 * @phpstan-param int<0,65535>|null $port
	 * @param UrlPath $path
	 * @param UrlQuery $query `null` は未指定
	 * @param string|null $fragment `null` は未指定
	 */
	public function __construct(
		public string $scheme,
		public string $user,
		public string $password,
		public string $host,
		public ?int $port,
		public UrlPath $path,
		public UrlQuery $query,
		public ?string $fragment
	) {
	}

	#region

	/**
	 * 配列からURL・文字列をデコードした値を取得。
	 *
	 * @param array<array-key,mixed> $elements
	 * @param string $key
	 * @param string|null $default
	 * @param UrlEncoding $urlEncoding
	 * @return string|null デコードされた値か、取得失敗時のデフォルト値
	 */
	private static function getDecodedValue(array $elements, string $key, ?string $default, UrlEncoding $urlEncoding): ?string
	{
		$rawElement = Arr::getOr($elements, $key, $default);
		if (Text::isNullOrWhiteSpace($rawElement)) {
			return $rawElement;
		}

		return $urlEncoding->decode($rawElement);
	}

	/**
	 * 文字列から生成。
	 *
	 * `parse_url` ラッパー。
	 *
	 * @param string $url URL 文字列。
	 * @param self|null $result 結果格納。成功時に格納される。
	 * @param UrlEncoding|null $urlEncoding URLエンコーディング。
	 * @return bool 成功。
	 * @phpstan-assert-if-true self $result
	 * @phpstan-assert-if-false null $result
	 */
	public static function tryParse(string $url, ?self &$result, ?UrlEncoding $urlEncoding = null): bool
	{
		$urlEncoding ??= UrlEncoding::createDefault();

		$elements = parse_url($url);
		if ($elements === false) {
			return false;
		}

		// さすがにこの二点は保証しておきたい(scheme は微妙と思いつつ)
		if (!isset($elements['scheme']) || Text::isNullOrWhiteSpace($elements['scheme'])) {
			return false;
		}
		if (!array_key_exists('host', $elements) || Text::isNullOrWhiteSpace($elements['host'])) {
			return false;
		}

		/** @var string */
		$user = self::getDecodedValue($elements, 'user', Text::EMPTY, $urlEncoding);
		/** @var string */
		$pass = self::getDecodedValue($elements, 'pass', Text::EMPTY, $urlEncoding);
		$fragment = self::getDecodedValue($elements, 'fragment', null, $urlEncoding);

		$result = new self(
			$elements['scheme'],
			$user,
			$pass,
			$elements['host'],
			Arr::getOr($elements, 'port', null),
			new UrlPath(Arr::getOr($elements, 'path', Text::EMPTY)),
			new UrlQuery(Arr::getOr($elements, 'query', null), $urlEncoding),
			$fragment
		);

		return true;
	}

	/**
	 * 文字列から生成。
	 *
	 * 失敗時に例外。
	 *
	 * @param string $url URL 文字列。
	 * @param UrlEncoding|null $urlEncoding URLエンコーディング。
	 * @return self
	 */
	public static function parse(string $url, ?UrlEncoding $urlEncoding = null): self
	{
		if (self::tryParse($url, $result, $urlEncoding)) {
			return $result;
		}
		throw new ParseException();
	}

	/**
	 * スキームを変更。
	 *
	 * @param non-empty-string $scheme
	 * @return self 変更された新規URL
	 */
	public function changeScheme(string $scheme): self
	{
		return new self(
			$scheme,
			$this->user,
			$this->password,
			$this->host,
			$this->port,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * 認証情報を変更。
	 *
	 * @param string $user
	 * @param string $password
	 * @return self 変更された新規URL
	 */
	public function changeAuthentication(string $user, string $password): self
	{
		return new self(
			$this->scheme,
			$user,
			$password,
			$this->host,
			$this->port,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * 認証情報を破棄。
	 *
	 * @return self 変更された新規URL
	 */
	public function clearAuthentication(): self
	{
		return $this->changeAuthentication(Text::EMPTY, Text::EMPTY);
	}


	/**
	 * ホストを変更。
	 *
	 * @param non-empty-string $host
	 * @return self 変更された新規URL
	 */
	public function changeHost(string $host): self
	{
		return new self(
			$this->scheme,
			$this->user,
			$this->password,
			$host,
			$this->port,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * ポートを変更。
	 *
	 * @param int $port
	 * @phpstan-param int<0,65535>|null $port
	 * @return self 変更された新規URL
	 */
	public function changePort(?int $port): self
	{
		return new self(
			$this->scheme,
			$this->user,
			$this->password,
			$this->host,
			$port,
			$this->path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * パスを変更。
	 *
	 * @param UrlPath $path
	 * @return self 変更された新規URL
	 */
	public function changePath(UrlPath $path): self
	{
		return new self(
			$this->scheme,
			$this->user,
			$this->password,
			$this->host,
			$this->port,
			$path,
			$this->query,
			$this->fragment
		);
	}

	/**
	 * クエリを変更。
	 *
	 * @param UrlQuery $query
	 * @return self 変更された新規URL
	 */
	public function changeQuery(UrlQuery $query): self
	{
		return new self(
			$this->scheme,
			$this->user,
			$this->password,
			$this->host,
			$this->port,
			$this->path,
			$query,
			$this->fragment
		);
	}

	/**
	 * フラグメントを変更。
	 *
	 * @param string|null $fragment
	 * @return self 変更された新規URL
	 */
	public function changeFragment(?string $fragment): self
	{
		return new self(
			$this->scheme,
			$this->user,
			$this->password,
			$this->host,
			$this->port,
			$this->path,
			$this->query,
			$fragment
		);
	}

	public function toString(?UrlEncoding $urlEncoding = null, bool $trailingSlash = false): string
	{
		$urlEncoding ??= UrlEncoding::createDefault();

		$work = $this->scheme . '://';

		$hasAuth = false;
		if (!Text::isNullOrEmpty($this->user)) {
			$work .= $urlEncoding->encode($this->user);
			$hasAuth = true;
		}
		if (!Text::isNullOrEmpty($this->password)) {
			$work .= ':' . $urlEncoding->encode($this->password);
			$hasAuth = true;
		}
		if ($hasAuth) {
			$work .= '@';
		}

		$work .= $this->host;
		if ($this->port !== null) {
			$work .= ':' . (string)$this->port;
		}
		if ($this->path->isEmpty() && $trailingSlash) {
			$work .= '/';
		} else {
			$work .= $this->path->toString($trailingSlash);
		}
		$work .= $this->query->toString($urlEncoding);

		if ($this->fragment !== null) {
			$work .= '#' . $urlEncoding->encode($this->fragment);
		}

		return $work;
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString();
	}

	#endregion
}
