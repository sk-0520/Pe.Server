<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Text;


/**
 * 要求パス。
 *
 * @immutable
 */
class RequestPath
{
	/**
	 * フルパス。
	 *
	 * @var string
	 */
	public string $full;
	/**
	 * / で分割されたパス一覧。
	 *
	 * @var string[]
	 */
	public array $tree;

	public function __construct(string $requestUri, string $skipPath)
	{
		$request = $requestUri;
		if (!Text::isNullOrWhiteSpace($skipPath)) {
			//TODO: リバースプロキシとかの場合, form のアクション、各リソースへのパスの書き換え未考慮 必要に迫られたら考える
		}

		$reqs = Text::split($request, '?', 2);

		$this->full = Text::trim($reqs[0], '/');
		$this->tree = Text::split($this->full, '/');
	}
}
