<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\Log\ILoggerFactory;

#[Version(-1)]
class SetupVersionLast extends SetupVersionBase
{
	public function __construct(private int $oldVersion, private int $newVersion, ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NONE
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

				SQL,
				[
					'version' => $this->newVersion,
				]
			);
		}
	}

	#endregion
}
