<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Web\UrlUtility;

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
	 * @param int $url URLエンコード種別
	 * @phpstan-param UrlUtility::URL_KIND_* $url
	 * @param Encoding $string 文字列エンコード種別
	 */
	public function __construct(
		public int $url,
		public Encoding $string
	) {
	}

	#region function

	public static function createDefault(): self
	{
		return new self(UrlUtility::URL_KIND_RFC3986, Encoding::getDefaultEncoding());
	}

	/**
	 * 文字コード変換してURLエンコードを行う。
	 *
	 * @param string $value
	 * @return string
	 */
	public function encode(string $value): string
	{
		$encodeString = $this->string->toBinary($value)->getRaw();
		$encodeValue = UrlUtility::encode($encodeString, $this->url);
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
		$decodeValue = new Binary(UrlUtility::decode($value, $this->url));
		$decodeString = $this->string->toString($decodeValue);

		return $decodeString;
	}

	#endregion
}
