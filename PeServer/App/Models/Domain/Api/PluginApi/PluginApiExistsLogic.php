<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\PluginApi;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Collection\Collections;
use PeServer\Core\Text;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Uuid;

class PluginApiExistsLogic extends ApiLogicBase
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
		$pluginId = $json['plugin_id'] ?? Text::EMPTY;
		$pluginName = $json['plugin_name'] ?? Text::EMPTY;

		$plugins = $this->dbCache->readPluginInformation();
		$pluginCollection = Collections::from($plugins->items);
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

	#endregion
}
