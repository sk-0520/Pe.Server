<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;

class AjaxPluginCategoryCreateLogic extends PageLogicBase
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
		$json = $this->getRequestJson();

		$params = [
			'plugin_category_id' => Text::trim(TypeUtility::toString($json['category_id'])),
			'category_display_name' => Text::trim(TypeUtility::toString($json['category_display_name'])),
			'category_description' => Text::trim(TypeUtility::toString($json['category_description'])),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($params) {
			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);

			$pluginCategoryId = $params['plugin_category_id'];
			$categoryDisplayName = $params['category_display_name'];
			$categoryDescription = $params['category_description'];

			$pluginCategoriesEntityDao->insertPluginCategory($pluginCategoryId, $categoryDisplayName, $categoryDescription);

			return true;
		});

		$this->setResponseJson(ResponseJson::success($params));

		$this->dbCache->exportPluginInformation();
	}

	#endregion
}
