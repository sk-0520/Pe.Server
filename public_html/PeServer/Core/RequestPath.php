<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

class RequestPath
{
	/**
	 * パス。
	 *
	 * @var string
	 */
	public string $path;
	/**
	 * 区切り分割されたパス一覧。
	 *
	 * @var string[]
	 */
	public array $tree;

	public function __construct(string $requestUri, string $skipPath)
	{
		$request = $requestUri;
		if (!StringUtility::isNullOrWhiteSpace($skipPath)) {
			//TODO: リバースプロキシとかの場合
		}

		$reqs = StringUtility::split($request, '?', 2);

		$this->path = StringUtility::trim($reqs[0], '/');
		$this->tree = explode('/', $this->path);
	}
}
