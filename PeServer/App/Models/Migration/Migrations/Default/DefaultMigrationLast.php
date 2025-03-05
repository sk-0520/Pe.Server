<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Migration\Migrations\LastMigrationTrait;
use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\IO\Directory;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(-1)]
class DefaultMigrationLast extends DefaultMigrationBase
{
	use MigrationTrait;
	use LastMigrationTrait;

	#region SetupVersionBase

	protected function migrateIOSystem(MigrationArgument $argument): void
	{
		// $this->logger->info('テンプレートキャッシュ全削除: {0}', $this->appConfig->setting->cache->template);
		// Directory::cleanupDirectory($this->appConfig->setting->cache->template);
	}

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$this->updateLastDatabase($this->version, $argument->context);

		// if ($this->oldVersion === $this->newVersion) {
		// 	return;
		// }

		// if ($this->oldVersion === -1) {
		// 	$argument->default->insertSingle(
		// 		<<<SQL

		// 		insert into
		// 			database_version
		// 			(
		// 				version
		// 			)
		// 			values
		// 			(
		// 				:version
		// 			)

		// 		SQL,
		// 		[
		// 			'version' => $this->newVersion,
		// 		]
		// 	);
		// } else {
		// 	$argument->default->updateByKey(
		// 		<<<SQL

		// 		update
		// 			database_version
		// 		set
		// 			version = :version
		// 		where
		// 			version < :version

		// 		SQL,
		// 		[
		// 			'version' => $this->newVersion,
		// 		]
		// 	);
		// }
	}

	#endregion
}
