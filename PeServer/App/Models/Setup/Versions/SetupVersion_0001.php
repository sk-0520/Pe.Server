<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

#[Version(1)]
class SetupVersion_0001 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(DatabaseSetupArgument $argument): void
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
			$argument->default->execute($statement);
		}
	}

	#endregion
}
