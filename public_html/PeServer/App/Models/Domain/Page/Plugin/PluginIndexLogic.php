<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class PluginIndexLogic extends PageLogicBase
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
		$this->setValue('link_default_plugin', false);

		if (!$this->dbCache->existsPluginInformation()) {
			$this->setValue('plugins', []);
			$this->setValue('link_default_plugin', true);
			$this->addError(Text::EMPTY, "プラグインなし");
			$this->addTemporaryMessage("この状態は原則ありえない(標準プラグインすら未登録状態)");
			return;
		}

		$pluginInformation = $this->dbCache->readPluginInformation();

		$plugins = Arr::sortCallbackByValue(
			$pluginInformation->items,
			fn (PluginCacheItem $a, PluginCacheItem $b)  => strcmp($a->pluginName, $b->pluginName)
		);

		$this->setValue('plugins', $plugins);
	}
}
