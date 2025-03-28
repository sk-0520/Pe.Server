<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class PluginUrlsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	/**
	 * Undocumented function
	 *
	 * @param string $pluginId
	 * @return array<string,string>
	 */
	public function selectUrls(string $pluginId): array
	{
		$results = $this->context->query(
			<<<SQL

			select
				plugin_urls.key,
				plugin_urls.url
			from
				plugin_urls
			where
				plugin_urls.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);

		/** @var array<string,string> */
		$map = [];
		foreach ($results->rows as $result) {
			$map[$result['key']] = $result['url'];
		}

		return $map;
	}

	public function insertUrl(string $pluginId, string $key, string $url): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				plugin_urls
				(
					plugin_id,
					key,
					url
				)
				values
				(
					:plugin_id,
					:key,
					:url
				)

			SQL,
			[
				'plugin_id' => $pluginId,
				'key' => $key,
				'url' => $url,
			]
		);
	}

	public function updatePluginUrl(string $pluginId, string $key, string $url): bool
	{
		return $this->context->updateByKeyOrNothing(
			<<<SQL

			update
				plugin_urls
			set
				url = :url
			where
				plugin_id = :plugin_id
				and
				key = :key

			SQL,
			[
				'plugin_id' => $pluginId,
				'key' => $key,
				'url' => $url,
			]
		);
	}

	public function deleteByPluginId(string $pluginId): int
	{
		return $this->context->delete(
			<<<SQL

			delete
			from
				plugin_urls
			where
				plugin_urls.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);
	}

	#endregion
}
