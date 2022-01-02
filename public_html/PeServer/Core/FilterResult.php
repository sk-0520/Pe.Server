<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Store\SessionStore;

/**
 * フィルタリング結果。
 */
class FilterResult
{
	public const RESULT_KIND_NONE = 0;
	public const RESULT_KIND_STATUS = 1;
	public const RESULT_KIND_LOGIC = 2;

	public HttpStatus $status;

	public function __construct(HttpStatus $status)
	{
		$this->status = $status;
	}
}
