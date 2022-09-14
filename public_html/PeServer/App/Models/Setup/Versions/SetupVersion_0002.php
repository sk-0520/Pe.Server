<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
#[Version(2)]
class SetupVersion_0002 extends SetupVersionBase
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NONE
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		$statements = <<<SQL

		create table
			[crash_reports]
			(
				[sequence] integer not null,

				[timestamp] datetime not null,
				[ip_address] text not null,

				[version] text not null,
				[revision] text not null,
				[build] text not null,
				[user_id] text not null,

				[exception] text not null,

				[email] text not null, -- メールアドレス(暗号化)
				[comment] text not null,

				[report] blob not null,

				primary key([sequence] autoincrement)
			)
		;

		create table
			[feedbacks]
			(
				[sequence] integer not null,

				[timestamp] datetime not null,
				[ip_address] text not null,

				[version] text not null,
				[revision] text not null,
				[build] text not null,
				[user_id] text not null,

				[first_execute_timestamp] text not null,
				[first_execute_version] text not null,

				[process] text not null,
				[platform] text not null,
				[os] text not null,
				[clr] text not null,

				[kind] text not null,
				[subject] text not null,
				[content] text not null,

				[detail] json not null,

				primary key([sequence] autoincrement)
			)
		;

		create table
			[feedback_attachments]
			(
				[sequence] integer not null,
				[feedback_sequence] integer not null,

				[mime] text not null,
				[name] text not null,
				[content] text not null,

				primary key([sequence] autoincrement)
				foreign key ([feedback_sequence]) references feedbacks([sequence])
			)
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
