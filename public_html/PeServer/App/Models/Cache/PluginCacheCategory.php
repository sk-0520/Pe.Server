<?php

declare(strict_types=1);

namespace PeServer\App\Models\Cache;

class PluginCacheCategory
{
	/**
	 * 生成。
	 *
	 * @param string $categoryId
	 * @param string $categoryName
	 * @param string $description
	 */
	public function __construct(
		public string $categoryId,
		public string $categoryName,
		public string $description
	) {
	}
}
