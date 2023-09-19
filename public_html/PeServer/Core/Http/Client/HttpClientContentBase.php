<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpHeadContentType;
use PeServer\Core\Text;

abstract class HttpClientContentBase
{
	#region function

	abstract public function toHeader(): HttpHeader;

	abstract public function toBody(): Binary;

	protected function createContentTypeHeader(string $mime, ?Encoding $encoding = null): HttpHeader
	{
		if (Text::isNullOrWhiteSpace($mime)) {
			return HttpHeader::createClientRequestHeader();
		}

		$result = HttpHeader::createClientRequestHeader();
		$result->setContentType(new HttpHeadContentType($mime, $encoding));

		return $result;
	}

	#endregion
}
