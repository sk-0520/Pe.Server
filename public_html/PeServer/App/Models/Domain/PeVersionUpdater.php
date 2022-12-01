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

	/**
	 * Undocumented function
	 *
	 * @param IDatabaseContext $context
	 * @param string $baseUrl
	 * @phpstan-param literal-string $baseUrl
	 * @param string $version
	 */
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

			// 標準テーマに更新URLは無視
			if($defaultPlugin->pluginId === '4524fc23-ebb9-4c79-a26b-8f472c05095e') {
				$url = '';
			}

			$pluginUrlsEntityDao->updatePluginUrl(
				$defaultPlugin->pluginId,
				PluginUrlKey::CHECK,
				$url
			);
		}


	}

	#endregion
}
