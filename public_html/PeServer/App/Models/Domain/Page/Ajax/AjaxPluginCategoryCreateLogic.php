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
			'plugin_category_id' => StringUtility::trim($json['category_id']),
			'category_display_name' => StringUtility::trim($json['category_display_name']),
			'category_description' => StringUtility::trim($json['category_description']),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);
			$pluginCategoriesEntityDao->insertPluginCategory($params['plugin_category_id'], $params['category_display_name'], $params['category_description']);

			return true;
		}, $params);

		$this->setResponseJson(ResponseJson::success($params));
	}
}
