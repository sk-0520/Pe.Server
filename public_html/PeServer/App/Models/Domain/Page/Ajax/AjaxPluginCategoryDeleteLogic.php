<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\Core\StringUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\Core\TypeUtility;

class AjaxPluginCategoryDeleteLogic extends PageLogicBase
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
		$params = [
			'plugin_category_id' => $this->getRequest('plugin_category_id'),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			/** @var array<string,mixed> $params*/

			$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);
			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);

			/** @var string */
			$pluginCategoryId = $params['plugin_category_id'];

			$pluginCategoryMappingsEntityDao->deletePluginCategoryMappings($pluginCategoryId);
			$pluginCategoriesEntityDao->deletePluginCategory($pluginCategoryId);

			return true;
		}, $params);

		$this->setResponseJson(ResponseJson::success($params));
	}
}
