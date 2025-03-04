<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

#[Version(3)]
class SetupVersion_0003 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		$statements = <<<SQL

		create table
			[pe_setting]
			(
				[version] text not null
			)
		;

		insert into
			[pe_setting]
			(
				[version]
			)
			values
			(
				'0.00.000'
			)

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
