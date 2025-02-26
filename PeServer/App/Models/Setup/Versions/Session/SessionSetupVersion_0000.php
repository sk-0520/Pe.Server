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

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NOP
	}

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
				[session] -- セッションデータ
				(
					[session_id] text not null, -- セッションID
					[create_timestamp] text not null, -- 作成日(UTC)
					[update_timestamp] text not null, -- 更新日(UTC)
					[data] json not null,
					primary key([session_id])
				)
			;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
