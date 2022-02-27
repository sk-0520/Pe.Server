<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class PluginCategoriesEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @return array<array{plugin_category_id:string,display_name:string,description:string}>
	 */
	public function selectAllPluginCategories(): array
	{
		/** @var array<array{plugin_category_id:string,display_name:string,description:string}> */
		return $this->context->selectOrdered(
			<<<SQL

			select
				plugin_categories.plugin_category_id,
				plugin_categories.display_name,
				plugin_categories.description
			from
				plugin_categories
			order by
				plugin_categories.plugin_category_id

			SQL,
			[]
		);
	}

	public function insertPluginCategory(string $pluginCategoryId, string $displayName, string $description): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				plugin_categories
				(
					plugin_category_id,
					display_name,
					description
				)
				values
				(
					:plugin_category_id,
					:display_name,
					:description
				)

			SQL,
			[
				'plugin_category_id' => $pluginCategoryId,
				'display_name' => $displayName,
				'description' => $description,
			]
		);
	}

	public function updatePluginCategory(string $pluginCategoryId, string $displayName, string $description): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				plugin_categories
			set
				display_name = :display_name,
				description = :description
			where
				plugin_category_id = :plugin_category_id

			SQL,
			[
				'plugin_category_id' => $pluginCategoryId,
				'display_name' => $displayName,
				'description' => $description,
			]
		);
	}

	public function deletePluginCategory(string $pluginCategoryId): void
	{
		$this->context->deleteByKey(
			<<<SQL

			delete
			from
				plugin_categories
			where
				plugin_category_id = :plugin_category_id

			SQL,
			[
				'plugin_category_id' => $pluginCategoryId,
			]
		);
	}
}
