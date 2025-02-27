<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions\Session;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Code;
use PeServer\Core\Regex;

#[Version(0)]
class SessionSetupVersion_0000 extends SessionSetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	/**
	 * Undocumented function
	 *
	 * @param DatabaseSetupArgument $argument
	 */
	protected function migrateDatabase(DatabaseSetupArgument $argument): void
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

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
