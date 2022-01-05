<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use PeServer\Core\Database;
use PeServer\App\Models\Database\Entities\PluginsEntityDao;
use PeServer\App\Models\Database\Entities\PluginUrlsEntityDao;


abstract class PluginUtility
{
	public static function removePlugin(Database $database, string $pluginId): void
	{
		$pluginsEntityDao = new PluginsEntityDao($database);
		$pluginUrlsEntityDao = new PluginUrlsEntityDao($database);

		$pluginUrlsEntityDao->deleteByPluginId($pluginId);
		$pluginsEntityDao->deletePlugin($pluginId);
	}
}
