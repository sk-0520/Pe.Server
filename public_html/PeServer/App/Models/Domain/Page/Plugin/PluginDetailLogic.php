<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Uuid;

class PluginDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$pluginInformation = $this->dbCache->readPluginInformation();
		/** @var PluginCacheItem */
		$plugin = Collection::from($pluginInformation->items)
			->first(function ($i) {
				return Uuid::isEqualGuid($i->pluginId, $this->getRequest('plugin_id'));
			});

		$categories = Collection::from($pluginInformation->categories)
			->where(fn(PluginCacheCategory $i) => ArrayUtility::in($plugin->categoryIds, $i->categoryId))
			->toArray()
		;
		usort($categories, function (PluginCacheCategory $a, PluginCacheCategory $b) {
			return $a->categoryName <=> $b->categoryName;
		});

		$this->setValue('plugin', $plugin);
		$this->setValue('categories', $categories);
	}
}
