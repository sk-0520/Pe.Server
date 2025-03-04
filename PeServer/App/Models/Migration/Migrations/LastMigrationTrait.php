<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations;

use PeServer\Core\Database\IDatabaseContext;

trait LastMigrationTrait
{
	#region function

	private function updateLastDatabase(int $version, IDatabaseContext $context): void
	{
		$existsDatabaseVersion = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
		if ($existsDatabaseVersion === 0) {
			$this->logger->info("INIT");
			$context->insertSingle(
				<<<SQL

				insert into
					database_version
					(
						version
					)
					values
					(
						:version
					)

				SQL,
				[
					'version' => $version,
				]
			);
		} else {
			$this->logger->info("UPDATE");
			$updateResult = $context->updateByKeyOrNothing(
				<<<SQL

				update
					database_version
				set
					version = :version
				where
					version < :version

				SQL,
				[
					'version' => $version,
				]
			);
		}
	}

	#endregion
}
