<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

class UserCacheItem
{
	public function __construct(
		public string $userId,
		public string $userName,
		public string $level,
		public string $state,
		public string $website,
		public string $description
	) {
	}
}
