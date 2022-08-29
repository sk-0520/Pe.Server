<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Text;
use PeServer\Core\Web\IUrlHelper;

/**
 * 要求パス。
 *
 * @immutable
 */
class RequestPath
{
	#region variable

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

	#endregion

	public function __construct(string $requestUri, private IUrlHelper $urlHelper)
	{
		$request = $requestUri;
		if (!Text::isNullOrWhiteSpace($this->urlHelper->getBasePath())) {
			//TODO: リバースプロキシとかの場合, form のアクション、各リソースへのパスの書き換え未考慮 必要に迫られたら考える
		}

		$reqs = Text::split($request, '?', 2);

		$this->full = Text::trim($reqs[0], '/');
		$this->tree = Text::split($this->full, '/');
	}
}
