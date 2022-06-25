<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\PluginApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Collection;
use PeServer\Core\Uuid;

class PluginApiInformationLogic extends ApiLogicBase
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
		$json = $this->getRequestJson();
		/** @var string[] */
		$pluginIds = ArrayUtility::getOr($json, 'plugin_ids', []);

		$plugins = AppDatabaseCache::readPluginInformation();
		/** @var array<string,array<mixed>> */
		$items = [];
		foreach($pluginIds as $pluginId) {
			foreach($plugins as $plugin) {
				if(Uuid::isEqualGuid($plugin->pluginId, $pluginId)) {
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
}
