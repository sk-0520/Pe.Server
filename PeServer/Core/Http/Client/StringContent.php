<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\BinaryContent;
use PeServer\Core\Text;

/**
 * 文字列本文データ。
 */
final class StringContent extends BinaryContent
{
	public function __construct(
		string $string,
		string $mime = Text::EMPTY,
		Encoding $encoding = null
	) {
		$encoding = $encoding ?? Encoding::getDefaultEncoding();
		$body = $encoding->getBinary($string);
		parent::__construct($body, $mime);
	}
}
