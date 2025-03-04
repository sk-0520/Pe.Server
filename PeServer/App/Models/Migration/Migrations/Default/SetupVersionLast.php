<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\IO\Directory;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Setup\MigrationVersion;

#[MigrationVersion(-1)]
class SetupVersionLast extends SetupVersionBase
{
	public function __construct(
		private int $oldVersion,
		private int $newVersion,
		AppConfiguration $appConfig,
		ILoggerFactory $loggerFactory
	) {
		parent::__construct($appConfig, $loggerFactory);
	}

	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		$this->logger->info('テンプレートキャッシュ全削除: {0}', $this->appConfig->setting->cache->template);
		Directory::cleanupDirectory($this->appConfig->setting->cache->template);
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		if ($this->oldVersion === $this->newVersion) {
			return;
		}

		if ($this->oldVersion === -1) {
			$argument->default->insertSingle(
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
					'version' => $this->newVersion,
				]
			);
		} else {
			$argument->default->updateByKey(
				<<<SQL

				update
					database_version
				set
					version = :version
				where
					version < :version

				SQL,
				[
					'version' => $this->newVersion,
				]
			);
		}
	}

	#endregion
}
