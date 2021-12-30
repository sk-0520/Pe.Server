<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\Store\CookieStore;
use \PeServer\Core\Store\SessionStore;

/**
 * フィルタリング時の入力パラメータ。
 */
class FilterResult
{
	public HttpStatus $status;

	public function __construct(HttpStatus $status)
	{
		$this->status = $status;
	}
}
