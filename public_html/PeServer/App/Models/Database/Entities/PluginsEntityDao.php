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
				plugins.name = :name

			SQL,
			[
				'name' => $pluginName,
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
					name,
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

	public function updatePluginEdit(string $pluginId, string $userId, string $displayName, string $description): void
	{
		$this->database->insertSingle(
			<<<SQL

			update
				plugins
			set
				display_name = :display_name,
				description = :description,
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
}
