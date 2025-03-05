<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Session;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Migration\Migrations\LastMigrationTrait;
use PeServer\App\Models\Migration\Migrations\Session\SessionMigrationBase;
use PeServer\Core\IO\Directory;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(-1)]
class SessionMigrationLast extends SessionMigrationBase
{
	use MigrationTrait;
	use LastMigrationTrait;

	#region SessionMigrationBase

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$this->updateLastDatabase($this->version, $argument->context);
	}

	#endregion
}
