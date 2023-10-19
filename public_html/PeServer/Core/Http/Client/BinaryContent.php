<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Text;

/**
 * バイナリ本文データ。
 */
class BinaryContent extends StaticContentBase
{
	public function __construct(
		Binary $binary,
		private string $mime = Text::EMPTY
	) {
		parent::__construct($binary);
	}

	#region StaticContentBase

	public function toHeader(): HttpHeader
	{
		return $this->createContentTypeHeader($this->mime);
	}

	#endregion
}
