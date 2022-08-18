<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class PluginIndexLogic extends PageLogicBase
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

		$items = $pluginInformation->items;

		usort($items, function (PluginCacheItem $a, PluginCacheItem $b) {
			return strcmp($a->pluginName, $b->pluginName);
		});

		$this->setValue('plugins', $items);
	}
}
