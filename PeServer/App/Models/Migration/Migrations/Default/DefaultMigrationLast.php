<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Migration\Migrations\LastMigrationTrait;
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

	#region DefaultMigrationBase

	protected function migrateIOSystem(MigrationArgument $argument): void
	{
		// $this->logger->info('テンプレートキャッシュ全削除: {0}', $this->appConfig->setting->cache->template);
		// Directory::cleanupDirectory($this->appConfig->setting->cache->template);
	}

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$this->updateLastDatabase($this->version, $argument->context);
	}

	#endregion
}
