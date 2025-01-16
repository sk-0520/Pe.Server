<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Web\UrlEncodeKind;

/**
 * URLエンコードを処理。
 *
 * URLエンコード処理と、処理後の文字列を対象の文字コードで云々する。
 */
readonly class UrlEncoding
{
	/**
	 * 生成。
	 *
	 * @param UrlEncodeKind $url URLエンコード種別
	 * @param Encoding $string 文字列エンコード種別
	 */
	public function __construct(
		public UrlEncodeKind $url,
		public Encoding $string
	) {
	}

	#region function

	public static function createDefault(): self
	{
		return new self(UrlEncodeKind::Rfc3986, Encoding::getDefaultEncoding());
	}

	/**
	 * URLエンコード。
	 *
	 * @param Binary $input
	 * @return string
	 * @phpstan-pure
	 * @see https://www.php.net/manual/function.urldecode.php
	 * @see https://www.php.net/manual/function.rawurldecode.php
	 */
	public function encodeUrl(Binary $input): string
	{
		return match ($this->url) {
			UrlEncodeKind::Rfc1738 => urlencode($input->raw),
			UrlEncodeKind::Rfc3986 => rawurlencode($input->raw),
		};
	}

	/**
	 * URLデコード。
	 *
	 * @param string $input
	 * @return Binary
	 * @see https://www.php.net/manual/function.urldecode.php
	 * @see https://www.php.net/manual/function.rawurldecode.php
	 */
	public function decodeUrl(string $input): Binary
	{
		$raw = match ($this->url) {
			UrlEncodeKind::Rfc1738 => urldecode($input),
			UrlEncodeKind::Rfc3986 => rawurldecode($input),
		};

		return new Binary($raw);
	}

	/**
	 * 文字コード変換してURLエンコードを行う。
	 *
	 * @param string $value
	 * @return string
	 */
	public function encode(string $value): string
	{
		$encodeString = $this->string->getBinary($value);
		$encodeValue = $this->encodeUrl($encodeString);

		return $encodeValue;
	}

	/**
	 * URLデコードして文字コード変換を行う。
	 *
	 * @param string $value
	 * @return string
	 */
	public function decode(string $value): string
	{
		$decodeValue = $this->decodeUrl($value);
		$decodeString = $this->string->toString($decodeValue);

		return $decodeString;
	}

	#endregion
}
