<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\StringUtility;


/**
 * 要求パス。
 */
class RequestPath
{
	/**
	 * フルパス。
	 *
	 * @var string
	 * @readonly
	 */
	public string $full;
	/**
	 * / で分割されたパス一覧。
	 *
	 * @var string[]
	 * @readonly
	 */
	public array $tree;

	public function __construct(string $requestUri, string $skipPath)
	{
		$request = $requestUri;
		if (!StringUtility::isNullOrWhiteSpace($skipPath)) {
			//TODO: リバースプロキシとかの場合, form のアクション、各リソースへのパスの書き換え未考慮 必要に迫られたら考える
		}

		$reqs = StringUtility::split($request, '?', 2);

		$this->full = StringUtility::trim($reqs[0], '/');
		$this->tree = StringUtility::split($this->full, '/');
	}
}
