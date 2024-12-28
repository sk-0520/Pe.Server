<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;

class PluginsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectExistsPluginId(string $pluginId): bool
	{
		return 0 < $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				plugins
			where
				plugins.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);
	}

	public function selectExistsPluginName(string $pluginName): bool
	{
		return 0 < $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				plugins
			where
				plugins.plugin_name = :plugin_name

			SQL,
			[
				'plugin_name' => $pluginName,
			]
		);
	}

	public function selectIsUserPlugin(string $pluginId, string $userId): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				plugins
			where
				plugins.plugin_id = :plugin_id
				and
				plugins.user_id = :user_id

			SQL,
			[
				'plugin_id' => $pluginId,
				'user_id' => $userId,
			]
		);
	}

	/**
	 * @template TFieldArray of array{plugin_id:string,plugin_name:string,display_name:string,state:string}
	 * @param string $userId
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function selectPluginByUserId(string $userId): DatabaseTableResult
	{
		/** @phpstan-var DatabaseTableResult<TFieldArray> */
		return $this->context->selectOrdered(
			<<<SQL

			select
				plugins.plugin_id,
				plugins.plugin_name,
				plugins.display_name,
				plugins.state
			from
				plugins
			where
				plugins.user_id = :user_id
			order by
				case plugins.state
					when 'enabled' then 10
					when 'check_failed' then 20
					when 'reserved' then 30
					when 'disabled' then 40
				end,
				plugins.plugin_name

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}

	/**
	 * @template TFieldArray of array{plugin_id:string,plugin_name:string,state:string}
	 * @param string $pluginId
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function selectPluginIds(string $pluginId): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		return $this->context->querySingle(
			<<<SQL

			select
				plugins.plugin_id,
				plugins.plugin_name,
				plugins.state
			from
				plugins
			where
				plugins.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId
			]
		);
	}

	/**
	 * @template TFieldArray of array{plugin_name:string,display_name:string,state:string,description:string}
	 * @param string $pluginId
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function selectEditPlugin(string $pluginId): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		return $this->context->querySingle(
			<<<SQL

			select
				plugins.plugin_name,
				plugins.display_name,
				plugins.state,
				plugins.description
			from
				plugins
			where
				plugins.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);
	}

	public function insertPlugin(string $pluginId, string $userId, string $pluginName, string $displayName, string $state, string $description, string $note): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				plugins
				(
					plugin_id,
					user_id,
					plugin_name,
					display_name,
					state,
					description,
					note
				)
				values
				(
					:plugin_id,
					:user_id,
					:plugin_name,
					:display_name,
					:state,
					:description,
					:note
				)

			SQL,
			[
				'plugin_id' => $pluginId,
				'user_id' => $userId,
				'plugin_name' => $pluginName,
				'display_name' => $displayName,
				'state' => $state,
				'description' => $description,
				'note' => $note,
			]
		);
	}

	public function updateEditPlugin(string $pluginId, string $userId, string $displayName, string $state, string $description): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				plugins
			set
				display_name = :display_name,
				state = :state,
				description = :description
			where
				plugins.plugin_id = :plugin_id
				and
				plugins.user_id = :user_id

			SQL,
			[
				'plugin_id' => $pluginId,
				'user_id' => $userId,
				'display_name' => $displayName,
				'state' => $state,
				'description' => $description,
			]
		);
	}

	public function deletePlugin(string $pluginId): void
	{
		$this->context->deleteByKey(
			<<<SQL

			delete
			from
				plugins
			where
				plugins.plugin_id = :plugin_id

			SQL,
			[
				'plugin_id' => $pluginId,
			]
		);
	}

	#endregion
}
