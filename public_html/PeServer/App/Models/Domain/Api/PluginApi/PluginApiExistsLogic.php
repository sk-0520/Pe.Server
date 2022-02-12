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

class PluginApiExistsLogic extends ApiLogicBase
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
		$plugin_id = ArrayUtility::getOr($json, 'plugin_id', '');
		$plugin_name = ArrayUtility::getOr($json, 'plugin_name', '');

		$plugins = AppDatabaseCache::readPluginInformation();
		$pluginCollection = Collection::from($plugins);
		$existsPluginId = $pluginCollection->any(function ($i) use ($plugin_id) {
			return Uuid::isEqualGuid($i->pluginId, $plugin_id);
		});
		$existsPluginName = $pluginCollection->any(function ($i) use ($plugin_name) {
			return $i->pluginName === $plugin_name;
		});

		$this->setResponseJson(ResponseJson::success([
			'plugin_id' => $existsPluginId,
			'plugin_name' => $existsPluginName,
		]));
	}
}
