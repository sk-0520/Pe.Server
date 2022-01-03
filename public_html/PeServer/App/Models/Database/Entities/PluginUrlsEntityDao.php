<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use PeServer\Core\DaoBase;
use PeServer\Core\Database;

class PluginUrlsEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	public function deleteByPluginId(string $pluginId): int
	{
		return $this->database->delete(
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

	public function insertUrl(string $pluginId, string $key, string $url): void
	{
		$this->database->insertSingle(
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
}
