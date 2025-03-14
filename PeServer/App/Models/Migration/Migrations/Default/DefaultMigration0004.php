<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(4)]
class DefaultMigration0004 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
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

		create table [feedback_comments] (
			[feedback_sequence] integer not null,
			[comment] text not null,
			foreign key([feedback_sequence]) references [feedbacks]([sequence]),
			primary key([feedback_sequence])
		)
		;

		create table [crash_report_comments] (
			[crash_report_sequence]	integer not null,
			[comment]	text not null,
			foreign key([crash_report_sequence]) references [crash_reports]([sequence]),
			primary key([crash_report_sequence])
		)
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
