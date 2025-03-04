<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions\Session;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\Session\SessionSetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\IO\Directory;
use PeServer\Core\Log\ILoggerFactory;

#[Version(-1)]
class SessionSetupVersionLast extends SessionSetupVersionBase
{
	public function __construct(
		private int $oldVersion,
		private int $newVersion,
		AppConfiguration $appConfig,
		ILoggerFactory $loggerFactory
	) {
		parent::__construct($appConfig, $loggerFactory);
	}

	#region SessionSetupVersionBase

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
