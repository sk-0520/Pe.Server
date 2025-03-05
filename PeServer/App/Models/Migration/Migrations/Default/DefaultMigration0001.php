<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(1)]
class DefaultMigration0001 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
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

		drop table
			[access_keys]
		;

		create table
			[api_keys]
			(
				[api_key] text not null, -- APIキー
				[user_id] text not null, -- ユーザーID
				[secret_key] text not null, -- シークレットキー
				[created_timestamp] datetime not null, -- 作成日
				primary key([api_key]),
				unique([api_key], [user_id]),
				foreign key ([user_id]) references users([user_id])
			)
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
