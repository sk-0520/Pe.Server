<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
#[Version(4)]
class SetupVersion_0004 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
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
			$argument->default->execute($statement);
		}
	}

	#endregion
}
