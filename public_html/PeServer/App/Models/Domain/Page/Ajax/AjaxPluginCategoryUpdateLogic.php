<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\SessionManager;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\StringUtility;

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
			'category_display_name' => StringUtility::trim($json['category_display_name']),
			'category_description' => StringUtility::trim($json['category_description']),
		];

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context, $params) {
			$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($context);
			$pluginCategoriesEntityDao->updatePluginCategory($params['plugin_category_id'], $params['category_display_name'], $params['category_description']);

			return true;
		}, $params);

		$this->setResponseJson(ResponseJson::success($params));
	}
}
