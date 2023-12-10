<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Collections\Collections;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class PluginDomainDao extends DaoBase
{
	use DaoTrait;

	#region function

	/**
	 * Undocumented function
	 *
	 * @return PluginCacheItem[]
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
			$categoryIds = $this->context->query(
				<<<SQL

				select
					plugin_categories.plugin_category_id
				from
					plugin_category_mappings
					inner join
						plugin_categories
						on
						(
							plugin_categories.plugin_category_id = plugin_category_mappings.plugin_category_id
						)
				where
					plugin_category_mappings.plugin_id = :plugin_id

				SQL,
				[
					'plugin_id' => $i['plugin_id']
				]
			);


			$cache = new PluginCacheItem(
				$i['plugin_id'],
				$i['user_id'],
				$i['plugin_name'],
				$i['display_name'],
				$i['state'],
				$i['description'],
				[
					PluginUrlKey::CHECK => $i['check_plugin_url'],
					PluginUrlKey::PROJECT => $i['project_plugin_url'],
					PluginUrlKey::LANDING => $i['lp_plugin_url'],
				],
				Collections::from($categoryIds->rows)
					->selectMany(fn($i) => $i)
					->toArray()
			);

			return $cache;
		}, $result->rows);
	}

	/**
	 * Undocumented function
	 *
	 * @return PluginCacheCategory[]
	 */
	public function selectCacheCategories(): array
	{
		$result = $this->context->query(
			<<<SQL

			select
				plugin_categories.plugin_category_id,
				plugin_categories.display_name,
				plugin_categories.description
			from
				plugin_categories
			order by
				plugin_categories.plugin_category_id

			SQL
		);

		return array_map(function ($i) {
			$cache = new PluginCacheCategory(
				$i['plugin_category_id'],
				$i['display_name'],
				$i['description']
			);

			return $cache;
		}, $result->rows);
	}

	#endregion
}
