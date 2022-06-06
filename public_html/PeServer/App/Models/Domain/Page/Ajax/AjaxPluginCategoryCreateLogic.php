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
use PeServer\Core\TypeConverter;

class AjaxPluginCategoryCreateLogic extends PageLogicBase
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

		$params = [
			'plugin_category_id' => StringUtility::trim(TypeConverter::toString($json['category_id'])),
			'category_display_name' => StringUtility::trim(TypeConverter::toString($json['category_display_name'])),
			'category_description' => StringUtility::trim(TypeConverter::toString($json['category_description'])),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			/** @var array<string,mixed> $params*/

			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);
			/** @var string */
			$pluginCategoryId = $params['plugin_category_id'];
			/** @var string */
			$categoryDisplayName = $params['category_display_name'];
			/** @var string */
			$categoryDescription = $params['category_description'];
			$pluginCategoriesEntityDao->insertPluginCategory($pluginCategoryId, $categoryDisplayName, $categoryDescription);

			return true;
		}, $params);

		$this->setResponseJson(ResponseJson::success($params));
	}
}
