<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Domain\DefaultPlugin;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Text;

class PeVersionUpdater
{
	#region function

	public function updateDatabase(IDatabaseContext $context, string $baseUrl, string $version): void
	{
		$peSettingEntityDao = new PeSettingEntityDao($context);
		$peSettingEntityDao->updatePeSettingVersion($version);

		$pluginUrlsEntityDao = new PluginUrlsEntityDao($context);

		$defaultPlugins = DefaultPlugin::get();
		foreach ($defaultPlugins as $defaultPlugin) {
			$url = Text::replaceMap(
				$baseUrl,
				[
					'VERSION' => $version,
					'UPDATE_INFO_NAME' => 'update-' . $defaultPlugin->pluginName . '.json',
				]
			);

			$pluginUrlsEntityDao->updatePluginUrl(
				$defaultPlugin->pluginId,
				PluginUrlKey::CHECK,
				$url
			);
		}


	}

	#endregion
}
