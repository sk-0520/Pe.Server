<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;

/**
 * ストア設定。
 * @immutable
 */
class StoreOption
{
	public function __construct(
		public CookieOption $cookie,
		public TemporaryOption $temporary,
		public SessionOption $session
	) {
	}
}
