<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\Core\StringUtility;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;

class AjaxPluginCategoryUpdateLogic extends PageLogicBase
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
			'plugin_category_id' => $this->getRequest('plugin_category_id'),
			'category_display_name' => StringUtility::trim(TypeUtility::toString($json['category_display_name'])),
			'category_description' => StringUtility::trim(TypeUtility::toString($json['category_description'])),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			/** @var array<string,mixed> $params*/

			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);

			$pluginCategoryId = TypeUtility::toString($params['plugin_category_id']);
			$categoryDisplayName = TypeUtility::toString($params['category_display_name']);
			$categoryDescription = TypeUtility::toString($params['category_description']);
			$pluginCategoriesEntityDao->updatePluginCategory($pluginCategoryId, $categoryDisplayName, $categoryDescription);

			return true;
		}, $params);

		$this->setResponseJson(ResponseJson::success($params));
	}
}
