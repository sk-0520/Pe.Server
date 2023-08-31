<?php

declare(strict_types=1);

namespace PeServer\App\Models\Setup\Versions;

use PeServer\App\Models\Setup\DatabaseSetupArgument;
use PeServer\App\Models\Setup\IOSetupArgument;
use PeServer\Core\Code;
use PeServer\Core\Regex;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 */
#[Version(0)]
class SetupVersion_0000 extends SetupVersionBase
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
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	protected function migrateDatabase(DatabaseSetupArgument $argument): void
	{
		//TODO: 全削除処理
		$tableNameResult = $argument->default->query(
			<<<SQL

			select
				sqlite_master.name as name
			from
				sqlite_master
			where
				sqlite_master.type='table'
				and
				sqlite_master.name <> 'sqlite_sequence'

			SQL
		);

		foreach ($tableNameResult->rows as $tableNameRow) {
			$tableName = $tableNameRow['name'];
			$argument->default->execute(Code::toLiteralString("drop table $tableName"));
		}

		//TODO: 暗号化とかとか
		$userId = '00000000-0000-4000-0000-000000000000';
		$loginId = 'setup_' . bin2hex(openssl_random_pseudo_bytes(4)) . '_' . date('YmdHis');
		$rawPassword = bin2hex(openssl_random_pseudo_bytes(4));
		$encPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

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

			create table
				[user_authentications] -- ユーザー認証情報
				(
					[user_id] text not null, -- ユーザーID
					[generated_password] text not null, -- 自動生成パスワード(ハッシュ) 空白の可能性あり(セットアップ・管理者等)
					[current_password] text not null, -- 現在パスワード(ハッシュ)
					primary key([user_id]),
					foreign key ([user_id]) references users([user_id])
				)
			;

			create table
				[access_keys]
				(
					[access_key] text not null, -- アクセスキー
					[user_id] text not null, -- ユーザーID
					primary key([access_key], [user_id]),
					unique([access_key]),
					foreign key ([user_id]) references users([user_id])
				)
			;

			create table
				[user_audit_logs] -- 監査ログ
				(
					[sequence] integer not null,
					[user_id] text not null, -- ユーザーID
					[timestamp] datetime not null, -- 書き込み日時(UTC)
					[event] text not null, -- イベント
					[info] text not null, -- 追加情報(JSON)
					[ip_address] text not null, -- クライアントIPアドレス
					[user_agent] text not null, -- クライアントUA
					primary key([sequence] autoincrement),
					foreign key ([user_id]) references users([user_id])
				)
			;

			create table
				[user_change_wait_emails] -- ユーザーメールアドレス変更確認
				(
					[user_id] text not null, -- ユーザーID
					[token] text not null, -- トークン
					[timestamp] text not null, -- トークン発行日時(UTC)
					[email] text not null, -- 変更後メールアドレス(暗号化)
					[mark_email] integer not null, -- 絞り込み用メールアドレス(ハッシュ:fnv)
					primary key([user_id]),
					foreign key ([user_id]) references users([user_id])
				)
			;

			create table
				[sign_up_wait_emails] -- 新規登録時のユーザーメールアドレス待機
				(
					[token] text not null, -- トークン
					[email] text not null, -- メールアドレス(暗号化)
					[mark_email] integer not null, -- 絞り込み用メールアドレス(ハッシュ:fnv)
					[timestamp] text not null, -- トークン発行日時(UTC)
					[ip_address] text not null, -- クライアントIPアドレス
					[user_agent] text not null, -- クライアントUA
					primary key([token])
				)
			;

			create index
				[sign_up_wait_emails_index_mark]
				on
					[sign_up_wait_emails]
					(
						[mark_email]
					)
			;

			create index
				[sign_up_wait_emails_index_search]
				on
					[sign_up_wait_emails]
					(
						[mark_email],
						[token]
					)
			;

			create table
				[plugins]
				(
					[plugin_id] text not null, -- プラグインID
					[user_id] text not null, -- プラグイン所有ユーザー
					[plugin_name] text not null, -- プラグイン名,
					[display_name] text not null, -- プラグイン表示名
					[state] text not null, -- 状態
					[description] text not null, -- 説明文
					[note] text not null, -- 管理者用メモ
					primary key([plugin_id]),
					unique ([plugin_name]),
					foreign key ([user_id]) references users([user_id])
				)
			;

			create table
				[plugin_urls]
				(
					[plugin_id] text not null, -- プラグインID
					[key] text not null, -- 種類
					[url] text not null, -- URL
					primary key([plugin_id], [key]),
					foreign key ([plugin_id]) references plugins([plugin_id])
				)
			;

			create table
				[plugin_categories]
				(
					[plugin_category_id] text not null, -- カテゴリID
					[display_name] text not null, -- 表示名
					[description] text not null, -- 説明文
					primary key([plugin_category_id])
				)
			;

			create table
				[plugin_category_mappings]
				(
					[plugin_id] text not null, -- プラグインID
					[plugin_category_id] text not null, -- カテゴリID
					primary key([plugin_id], [plugin_category_id]),
					foreign key ([plugin_id]) references plugins([plugin_id])
					foreign key ([plugin_category_id]) references plugin_categories([plugin_category_id])
				)
			;

			insert into
				[users]
				(
					[user_id],
					[login_id],
					[level],
					[state],
					[name],
					[email],
					[mark_email],
					[website],
					[description],
					[note]
				)
				values
				(
					{$argument->default->escapeValue($userId)},
					{$argument->default->escapeValue($loginId)},
					'setup',
					'enabled',
					'setup user',
					'',
					0,
					'',
					'',
					''
				)
			;

			insert into
				[user_authentications]
				(
					[user_id],
					[generated_password],
					[current_password]
				)
				values
				(
					{$argument->default->escapeValue($userId)},
					'',
					{$argument->default->escapeValue($encPassword)}
				)
			;

			insert into
				[plugin_categories]
				(
					[plugin_category_id],
					[display_name],
					[description]
				)
				values
				( 'theme', 'テーマ', '' ),
				--
				( 'file', 'ファイル', '' ),
				( 'network', 'ネットワーク', '' ),
				( 'news', 'ニュース', '' ),
				( 'search', '検索', '' ),
				( 'system', 'システム', '' ),
				--
				( 'utility', 'ユーティリティ', '' ),
				( 'toy', 'おもちゃ', '' )
			;

		SQL;

		foreach ($this->splitStatements($statements) as $statement) {
			$argument->default->execute($statement);
		}

		$this->logger->info('SETUP LOG');
		$this->logger->info([
			'user_id' => $userId,
			'login_id' => $loginId,
			'password' => $rawPassword,
		]);
	}

	#endregion
}
