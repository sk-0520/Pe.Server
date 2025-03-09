<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Session;

use PeServer\Core\Code;
use PeServer\Core\Regex;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(1)]
class SessionMigration0001 extends SessionMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	use MigrationTrait;

	#region SessionMigrationBase

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$statements = <<<SQL

			drop index
				[sessions_logs_idx_updated]
			;

			create index
				[sessions_idx_updated] on [sessions]
				(
					[updated_epoch]
				)
			;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
