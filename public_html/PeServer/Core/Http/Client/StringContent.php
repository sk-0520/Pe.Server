<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\StaticContentBase;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Text;

class StringContent extends StaticContentBase
{
	public function __construct(
		string $string,
		private string $mime = Text::EMPTY,
		Encoding $encoding = null
	) {
		$encoding = $encoding ?? Encoding::getDefaultEncoding();

		$body = $encoding->toBinary($string);
		parent::__construct($body);
	}

	#region StaticContentBase

	public function toHeader(): HttpHeader
	{
		return $this->createContentTypeHeader($this->mime);
	}

	#endregion
}
