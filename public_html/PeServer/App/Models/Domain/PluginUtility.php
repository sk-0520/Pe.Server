<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\Core\Database\IDatabaseContext;

abstract class PluginUtility
{
	public static function removePlugin(IDatabaseContext $database, string $pluginId): void
	{
		$pluginsEntityDao = new PluginsEntityDao($database);
		$pluginUrlsEntityDao = new PluginUrlsEntityDao($database);
		$pluginCategoryMappingsEntityDao = new PluginCategoryMappingsEntityDao($database);

		$pluginCategoryMappingsEntityDao->deletePluginCategoryMappings($pluginId);
		$pluginUrlsEntityDao->deleteByPluginId($pluginId);
		$pluginsEntityDao->deletePlugin($pluginId);
	}
}
