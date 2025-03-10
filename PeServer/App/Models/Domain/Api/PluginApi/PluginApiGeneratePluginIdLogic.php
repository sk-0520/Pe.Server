<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\PluginApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Uuid;

class PluginApiGeneratePluginIdLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$plugins = $this->dbCache->readPluginInformation();
		$pluginCollection = Collection::from($plugins->items);

		$pluginId = Uuid::generateGuid();

		$existsPluginId = true;
		do {
			$existsPluginId = $pluginCollection->any(function ($i) use ($pluginId) {
				return Uuid::isEqualGuid($i->pluginId, $pluginId);
			});

			if ($existsPluginId) {
				$this->logger->info('重複プラグインID -> {0}', $pluginId);
				$pluginId = Uuid::generateGuid();
			}
		} while ($existsPluginId);

		$this->setResponseJson(ResponseJson::success([
			'plugin_id' => $pluginId,
		]));
	}

	#endregion
}
