<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\PluginApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Collections\Collection;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Uuid;

class PluginApiExistsLogic extends ApiLogicBase
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
		$json = $this->getRequestJson();
		$pluginId = ArrayUtility::getOr($json, 'plugin_id', DefaultValue::EMPTY_STRING);
		$pluginName = ArrayUtility::getOr($json, 'plugin_name', DefaultValue::EMPTY_STRING);

		$plugins = $this->dbCache->readPluginInformation();
		$pluginCollection = Collection::from($plugins->items);
		$existsPluginId = $pluginCollection->any(function ($i) use ($pluginId) {
			return Uuid::isEqualGuid($i->pluginId, $pluginId);
		});
		$existsPluginName = $pluginCollection->any(function ($i) use ($pluginName) {
			return $i->pluginName === $pluginName;
		});

		$this->setResponseJson(ResponseJson::success([
			'plugin_id' => $existsPluginId,
			'plugin_name' => $existsPluginName,
		]));
	}
}
