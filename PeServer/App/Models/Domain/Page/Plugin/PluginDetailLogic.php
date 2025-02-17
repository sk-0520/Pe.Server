<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\Collections;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Uuid;

class PluginDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$pluginInformation = $this->dbCache->readPluginInformation();
		/** @var PluginCacheItem */
		$plugin = Collections::from($pluginInformation->items)
			->first(function ($i) {
				return Uuid::isEqualGuid($i->pluginId, $this->getRequest('plugin_id'));
			});

		$categories = Collections::from($pluginInformation->categories)
			->where(fn(PluginCacheCategory $i) => Arr::in($plugin->categoryIds, $i->categoryId))
			->toArray()
		;
		$categories = Arr::sortCallbackByValue($categories, function (PluginCacheCategory $a, PluginCacheCategory $b) {
			return $a->categoryName <=> $b->categoryName;
		});

		$this->setValue('plugin', $plugin);
		$this->setValue('categories', $categories);
	}
}
