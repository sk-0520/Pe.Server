<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(7)]
class DefaultMigration0007 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
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
			[report_status]
			(
				[value] text not null
			)
		;

		insert into
			[report_status]
			(
				[value]
			)
			values
			(
				'none'
			),
			(
				'working'
			),
			(
				'completed'
			),
			(
				'ignore'
			)
		;

		create table
			[feedback_status]
			(
				[feedback_sequence] integer not null,
				[status] text not null,
				foreign key([feedback_sequence]) references [feedbacks]([sequence]),
				foreign key([status]) references [report_status]([value]),
				primary key([feedback_sequence])
			)
		;

		create table
			[crash_report_status]
			(
				[crash_report_sequence]	integer not null,
				[status] text not null,
				foreign key([crash_report_sequence]) references [crash_reports]([sequence]),
				foreign key([status]) references [report_status]([value]),
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
