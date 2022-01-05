<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use PeServer\Core\DaoBase;
use PeServer\Core\Database;

class PluginsEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	public function selectExistsPluginId(string $pluginId): bool
	{
		return 0 < $this->database->selectSingleCount(
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
		return 0 < $this->database->selectSingleCount(
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
		return 1 === $this->database->selectSingleCount(
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
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return array<array{plugin_id:string,plugin_name:string,display_name:string,state:string}>
	 */
	public function selectPluginByUserId(string $userId): array
	{
		return $this->database->selectOrdered(
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
					when 'enabled' then 1
					when 'check_failed' then 2
					when 'disabled' then 3
				end,
				plugins.plugin_name

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $pluginId
	 * @return array{plugin_id:string,plugin_name:string,state:string}
	 */
	public function selectPluginIds(string $pluginId): array
	{
		/** @var array{plugin_id:string,plugin_name:string,state:string} */
		return $this->database->querySingle(
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
	 * Undocumented function
	 *
	 * @param string $pluginId
	 * @return array{plugin_name:string,display_name:string,description:string}
	 */
	public function selectEditPlugin(string $pluginId): array
	{
		/** @var array{plugin_name:string,display_name:string,description:string} */
		return $this->database->querySingle(
			<<<SQL

			select
				plugins.plugin_name,
				plugins.display_name,
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
		$this->database->insertSingle(
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

	public function updateEditPlugin(string $pluginId, string $userId, string $displayName, string $description): void
	{
		$this->database->updateByKey(
			<<<SQL

			update
				plugins
			set
				display_name = :display_name,
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
				'description' => $description,
			]
		);
	}

	public function deletePlugin(string $pluginId): void
	{
		$this->database->deleteByKey(
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
}
