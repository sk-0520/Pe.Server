<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

use PeServer\App\Models\Cache\UserCacheItem;

class UserCache
{
	/**
	 * 生成。
	 *
	 * @param UserCacheItem[] $items
	 */
	public function __construct(
		public array $items
	) {
	}
}
