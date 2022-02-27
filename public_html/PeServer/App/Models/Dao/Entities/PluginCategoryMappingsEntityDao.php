<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class PluginCategoryMappingsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $pluginId
	 * @return array<string>
	 */
	public function selectPluginCategoriesByPluginId(string $pluginId): array
	{
		$results = $this->context->query(
			<<<SQL

			select
				plugin_category_mappings.plugin_category_id
			from
				plugin_category_mappings
			where
				plugin_category_mappings.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);

		return array_map(function ($i) {
			return $i['plugin_category_id'];
		}, $results);
	}

	public function insertPluginCategoryMapping(string $pluginId, string $pluginCategoryId): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				plugin_category_mappings
				(
					plugin_id,
					plugin_category_id
				)
				values
				(
					:plugin_id,
					:plugin_category_id
				)

			SQL,
			[
				'plugin_id' => $pluginId,
				'plugin_category_id' => $pluginCategoryId,
			]
		);
	}

	public function deletePluginCategoryMappings(string $pluginId): int
	{
		return $this->context->delete(
			<<<SQL

			delete
			from
				plugin_category_mappings
			where
				plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);
	}
}
