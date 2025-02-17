<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\PluginApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Uuid;

class PluginApiInformationLogic extends ApiLogicBase
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
		$json = $this->getRequestJson();
		/** @var string[] */
		$pluginIds = $json['plugin_ids'];

		$plugins = $this->dbCache->readPluginInformation();
		/** @var array<string,array<mixed>> */
		$items = [];
		foreach ($pluginIds as $pluginId) {
			foreach ($plugins->items as $plugin) {
				if (Uuid::isEqualGuid($plugin->pluginId, $pluginId)) {
					$items[$plugin->pluginId] = [
						'user_id' => $plugin->userId,
						'plugin_name' => $plugin->pluginName,
						'display_name' => $plugin->displayName,
						'state' => $plugin->state,
						'description' => $plugin->description,
						'check_url' => $plugin->urls['check'],
						'project_url' => $plugin->urls['project'],
					];
					break;
				}
			}
		}


		$this->setResponseJson(ResponseJson::success([
			'plugins' => $items,
		]));
	}

	#endregion
}
