<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Session;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Code;
use PeServer\Core\Regex;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(0)]
class SessionMigration0000 extends SessionMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	use MigrationTrait;

	#region SetupVersionBase

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$statements = <<<SQL

			create table
				[database_version] -- DBバージョン
				(
					[version] integer not null
				)
			;

			create table
				[sessions] -- セッションデータ
				(
					[session_id] text not null, -- セッションID
					[created_epoch] integer not null, -- 作成日(UNIX時間)
					[updated_epoch] integer not null, -- 更新日(UNIX時間)
					[data] text not null,
					primary key([session_id])
				)
			;

			create index
				[sessions_logs_idx_updated] on [sessions]
				(
					[updated_epoch]
				)
			;

			create view
				view_sessions
			as
				select
					sessions.session_id,
					datetime(sessions.created_epoch, 'unixepoch') || 'Z' as created_timestamp,
					datetime(sessions.updated_epoch, 'unixepoch') || 'Z' as updated_timestamp,
					sessions.data
				from
					sessions
				order by
					sessions.updated_epoch,
					sessions.created_epoch,
					sessions.session_id
			;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
