<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(3)]
class DefaultMigration0003 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	use MigrationTrait;

	#region SetupVersionBase

	protected function migrateIOSystem(MigrationArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(MigrationArgument $argument): void
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
			$argument->context->execute($statement);
		}
	}

	#endregion
}
