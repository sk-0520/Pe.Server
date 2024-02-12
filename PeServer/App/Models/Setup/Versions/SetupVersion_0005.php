<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
#[Version(5)]
class SetupVersion_0005 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
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
			[BK_user_authentications]
		as
			select
				*
			from
				[user_authentications]
		;

		drop table
			[user_authentications]
		;

		create table
			[user_authentications] -- ユーザー認証情報
			(
				[user_id] text not null, -- ユーザーID
				[reminder_token] text not null, -- パスワードリマインダー トークン
				[reminder_timestamp] datetime, -- パスワードリマインダー 作成日時
				[current_password] text not null, -- 現在パスワード(ハッシュ)
				primary key([user_id]),
				foreign key ([user_id]) references users([user_id])
			)
		;

		insert into
			[user_authentications]
			(
				[user_id],
				[reminder_token],
				[reminder_timestamp],
				[current_password]
			)
			select
				[BK_user_authentications].[user_id],
				'',
				NULL,
				[BK_user_authentications].[current_password]
			from
				[BK_user_authentications]
		;

		drop table
			[BK_user_authentications]
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
