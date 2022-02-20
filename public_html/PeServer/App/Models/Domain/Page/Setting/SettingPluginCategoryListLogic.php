<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\Core\FileUtility;
use PeServer\Core\SizeConverter;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;

class SettingPluginCategoryListLogic extends PageLogicBase
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
		$database = $this->openDatabase();

		$pluginCategoriesEntityDao = new PluginCategoriesEntityDao($database);
		$categories = $pluginCategoriesEntityDao->selectAllPluginCategories();

		$this->setValue('categories', $categories);
	}
}
