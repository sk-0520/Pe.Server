<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Plugin;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Uuid;

class PluginDetailLogic extends PageLogicBase
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
		$plugin = Collection::from($pluginInformation)
			->first(function ($i) {
				return Uuid::isEqualGuid($i->pluginId, $this->getRequest('plugin_id'));
			});

		$this->setValue('plugin', $plugin);
	}
}
