<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Collections\Dictionary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Mime;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;

/**
 * 構築時点でもう粗方確定している本文データ。
 *
 * 本文データ自体は継承側コンストラクタで構築すること。
 */
abstract class StaticContentBase extends HttpClientContentBase
{
	#region variable

	readonly protected Binary $body;

	#endregion

	protected function __construct(Binary $body)
	{
		$this->body = $body;
	}

	#region HttpClientContentBase

	public function toBody(): Binary
	{
		return $this->body;
	}

	#endregion
}
