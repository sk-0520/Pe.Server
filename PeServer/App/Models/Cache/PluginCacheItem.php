<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

class PluginCacheItem
{
	/**
	 * 生成。
	 *
	 * @param string $pluginId
	 * @param string $userId
	 * @param string $pluginName
	 * @param string $displayName
	 * @param string $state
	 * @param string $description
	 * @param array<string,string> $urls
	 * @param string[] $categoryIds
	 */
	public function __construct(
		public string $pluginId,
		public string $userId,
		public string $pluginName,
		public string $displayName,
		public string $state,
		public string $description,
		public array $urls,
		public array $categoryIds
	) {
	}
}
