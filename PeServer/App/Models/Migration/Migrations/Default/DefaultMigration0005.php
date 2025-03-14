<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;

#[MigrationVersion(5)]
class DefaultMigration0005 extends DefaultMigrationBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
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
			$argument->context->execute($statement);
		}
	}

	#endregion
}
