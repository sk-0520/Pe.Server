<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Domain\Page\PageLogicBase;

class PluginIndexLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$pluginInformation = AppDatabaseCache::readPluginInformation();

		usort($pluginInformation, function(PluginCache $a, PluginCache $b) {
			return strcmp($a->pluginName, $b->pluginName);
		});

		$this->setValue('plugins', $pluginInformation);
	}
}
