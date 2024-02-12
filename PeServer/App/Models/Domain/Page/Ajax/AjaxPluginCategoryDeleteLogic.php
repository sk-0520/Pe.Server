<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\TypeUtility;

class AjaxPluginCategoryDeleteLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$params = [
			'plugin_category_id' => $this->getRequest('plugin_category_id'),
		];

		if (Text::isNullOrWhiteSpace($params['plugin_category_id'])) {
			throw new HttpStatusException(HttpStatus::BadRequest);
		}

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($context);
			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);

			$pluginCategoryId = $params['plugin_category_id'];

			$pluginCategoryMappingsEntityDao->deletePluginCategoryMappings($pluginCategoryId);
			$pluginCategoriesEntityDao->deletePluginCategory($pluginCategoryId);

			return true;
		});

		$this->setResponseJson(ResponseJson::success($params));

		$this->dbCache->exportPluginInformation();
	}

	#endregion
}
