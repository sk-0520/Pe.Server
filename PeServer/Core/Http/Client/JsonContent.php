<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Mime;
use PeServer\Core\Serialization\JsonSerializer;

class JsonContent extends StaticContentBase
{
	/**
	 * 生成。
	 *
	 * @param array<mixed>|object $value
	 * @param string $mime
	 * @param JsonSerializer|null $jsonSerializer
	 */
	public function __construct(
		array|object $value,
		private string $mime = Mime::JSON,
		?JsonSerializer $jsonSerializer = null
	) {
		$jsonSerializer = $jsonSerializer ?? new JsonSerializer();

		$body = $jsonSerializer->save($value);
		parent::__construct($body);
	}

	#region StaticContentBase

	public function toHeader(): HttpHeader
	{
		return $this->createContentTypeHeader($this->mime);
	}

	#endregion
}
