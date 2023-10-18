<?php

declare(strict_types=1);

namespace PeServer\Core\Http\Client;

use PeServer\Core\Binary;

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
