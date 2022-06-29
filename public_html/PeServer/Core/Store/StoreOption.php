<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionOption;
use PeServer\Core\Store\TemporaryOption;

/**
 * ストア設定。
 */
class StoreOption
{
	public function __construct(
		/** @readonly */
		public CookieOption $cookie,
		/** @readonly */
		public TemporaryOption $temporary,
		/** @readonly */
		public SessionOption $session
	) {
	}
}
