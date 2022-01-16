<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use PeServer\Core\Database\DaoBase;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Database\IDatabaseContext;

class PluginDomainDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @return PluginCache[]
	 */
	public function selectCacheItems(): array
	{
		$result = $this->context->query(
			<<<SQL

			select
				plugins.plugin_id,
				plugins.user_id,
				plugins.plugin_name,
				plugins.display_name,
				plugins.state,
				plugins.description,
				check_plugin_urls.url as check_plugin_url,
				project_plugin_urls.url as project_plugin_url,
				lp_plugin_urls.url as lp_plugin_url
			from
				plugins
				left join
					plugin_urls as check_plugin_urls
					on
					(
						check_plugin_urls.plugin_id = plugins.plugin_id
						and
						check_plugin_urls.key = :url_check
					)
				left join
					plugin_urls as project_plugin_urls
					on
					(
						project_plugin_urls.plugin_id = plugins.plugin_id
						and
						project_plugin_urls.key = :url_project
					)
				left join
					plugin_urls as lp_plugin_urls
					on
					(
						lp_plugin_urls.plugin_id = plugins.plugin_id
						and
						lp_plugin_urls.key = :url_lp
					)
				order by
					plugins.plugin_id

			SQL,
			[
				'url_check' => PluginUrlKey::CHECK,
				'url_project' => PluginUrlKey::PROJECT,
				'url_lp' => PluginUrlKey::LANDING,
			]
		);

		return array_map(function ($i) {
			$cache = new PluginCache();

			$cache->pluginId = $i['plugin_id'];
			$cache->userId = $i['user_id'];
			$cache->pluginName = $i['plugin_name'];
			$cache->displayName = $i['display_name'];
			$cache->state = $i['state'];
			$cache->description = $i['description'];
			$cache->urls = [
				PluginUrlKey::CHECK => $i['check_plugin_url'],
				PluginUrlKey::PROJECT => $i['project_plugin_url'],
				PluginUrlKey::LANDING => $i['lp_plugin_url'],
			];

			return $cache;
		}, $result);
	}
}
