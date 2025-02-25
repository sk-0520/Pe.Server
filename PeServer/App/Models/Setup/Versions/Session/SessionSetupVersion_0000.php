<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions\Session;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\App\Models\Setup\Versions\SetupVersionBase;
use PeServer\App\Models\Setup\Versions\Version;
use PeServer\Core\Code;
use PeServer\Core\Regex;

#[Version(0)]
class SetupVersion_0000 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	protected function migrateIOSystem(IOSetupArgument $argument): void
	{
		//NOP
	}

	/**
	 * Undocumented function
	 *
	 * @param DatabaseSetupArgument $argument
	 */
	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		$statements = <<<SQL

			create table
				[database_version] -- DBバージョン
				(
					[version] integer not null
				)
			;

			create table
				[users] -- ユーザー情報
				(
					[user_id] text not null, -- ユーザーID
					[login_id] text not null, -- ログインID
					[level] text not null, -- ユーザーレベル(権限てきな)
					[state] text not null, -- 状態
					[name] text not null, -- 名前
					[email] text not null, -- メールアドレス(暗号化)
					[mark_email] integer not null, -- 絞り込み用メールアドレス(ハッシュ:fnv)
					[website] text not null, -- Webサイト
					[description] text not null, -- 説明文
					[note] text not null, -- 管理者用メモ
					primary key([user_id]),
					unique([login_id])
				)
			;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}
	}

	#endregion
}
