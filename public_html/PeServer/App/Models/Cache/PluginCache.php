<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;

class PluginCache
{
	/**
	 * 生成。
	 *
	 * @param PluginCacheCategory[] $categories プラグインカテゴリ一覧。
	 * @param PluginCacheItem[] $items プラグインアイテム一覧。
	 */
	public function __construct(
		public array $categories,
		public array $items
	) {
	}
}
