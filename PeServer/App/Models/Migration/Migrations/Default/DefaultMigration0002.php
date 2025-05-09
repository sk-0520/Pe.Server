<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(2)]
class DefaultMigration0002 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	use MigrationTrait;

	#region DefaultMigrationBase

	protected function migrateIOSystem(MigrationArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(MigrationArgument $argument): void
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

				primary key([sequence] autoincrement)
			)
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
