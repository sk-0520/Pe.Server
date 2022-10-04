<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
#[Version(3)]
class SetupVersion_0003 extends SetupVersionBase
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
			[pe_setting]
			(
				[version] text not null,
				[application_info_url] text not null
			)
		;

		insert into
			[pe_setting]
			(
				[version],
				[application_info_url]
			)
			values
			(
				'0.00.000',
				'http://example.com/'
			)

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
