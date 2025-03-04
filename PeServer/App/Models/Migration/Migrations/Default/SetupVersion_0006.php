<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations\Default;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\Setup\MigrationArgument;
use PeServer\Core\Setup\MigrationVersion;

#[MigrationVersion(6)]
class SetupVersion_0006 extends SetupVersionBase //phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
{
	#region SetupVersionBase

	protected function migrateIOSystem(MigrationArgument $argument): void
	{
		//NOP
	}

	protected function migrateDatabase(MigrationArgument $argument): void
	{
		$statements = <<<SQL

		create table
			[access_logs] -- ユーザー認証情報
			(
				[timestamp] text not null, -- タイムスタンプ
				[client_ip] text not null, -- クライアントIP
				[client_host] text not null, -- クライアントホスト
				[request_id] text not null, -- リクエストID
				[session] text not null, -- セッションID
				[ua] text not null, -- UA
				[method] text not null, -- メソッド
				[path] text not null, -- パス
				[query] text not null, -- クエリ
				[fragment] text not null, -- フラグメント
				[referer] text not null, -- リファラ
				[running_time] read not null -- 実行時間(ミリ秒)
			)
		;

		create index
			[access_logs_idx_timestamp] on [access_logs]
			(
				[timestamp]
			)
		;

		create index
			[access_logs_idx_ua] on [access_logs]
			(
				[ua]
			)
		;

		create index
			[access_logs_idx_path] on [access_logs]
			(
				[path]
			)
		;

		create index
			[access_logs_idx_search_1] on [access_logs]
			(
				[timestamp],
				[path]
			)
		;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->context->execute($statement);
		}
	}

	#endregion
}
