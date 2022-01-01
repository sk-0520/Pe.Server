<?php

use Deploy\FileUtility;

class DeployScript
{
	private $scriptArgument;
	private $fileTimestampName;

	public function __construct(\Deploy\ScriptArgument $scriptArgument)
	{
		$this->scriptArgument = $scriptArgument;
		$this->fileTimestampName = date("Y-m-d_His");
	}

	private function getAppDirectoryPath(): string
	{
		return $this->scriptArgument->joinPath($this->scriptArgument->publicDirectoryPath, 'PeServer');
	}

	public function before(): void
	{
		$this->scriptArgument->log('before script');

		// 不要データの破棄
		$removeDirs = [
			$this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data', 'temp', 'views'),
			$this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data', 'store', 'temporary'), // 一時データのなんぞ知らんわ
		];

		foreach ($removeDirs as $removeDir) {
			$this->scriptArgument->log('remove: ' . $removeDir);
			$this->scriptArgument->cleanupDirectory($removeDir);
		}

		$backupDirectoryPath = $this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data', 'backups');
		if (!file_exists($backupDirectoryPath)) {
			mkdir($backupDirectoryPath, 0777, true);
		}
		$backupArchiveFilePath = $this->scriptArgument->joinPath($backupDirectoryPath, $this->fileTimestampName . '.zip');

		// バックアップ
		$srcBackupPaths = [
			$this->scriptArgument->joinPath('PeServer', 'data', 'data.sqlite3'),
		];
		$this->scriptArgument->backupFiles($backupArchiveFilePath, $srcBackupPaths);
	}

	public function after(): void
	{
		$this->scriptArgument->log('after script');

		// 設定のマージとかしんどいので直接書いとく。
		$this->migrate([
			'connection' => 'sqlite:' . $this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data/data.sqlite3'),
			'user' => '',
			'passwd' => '',
		]);
	}

	/**
	 * マイグレーション
	 * PeServerからも使用される
	 *
	 * @param {driver:string,connection:string,user:string,password:string} $databaseSetting
	 * @return void
	 */
	public function migrate(array $databaseSetting)
	{
		$this->db_migrate($databaseSetting);
	}

	private function createConnection(array $databaseSetting): PDO
	{
		$pdo = new PDO($databaseSetting['connection']/*, null, null, [
			PDO::ATTR_PERSISTENT => true,
		]*/);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $pdo;
	}

	/**
	 * @param {driver:string,connection:string,user:string,password:string} $databaseSetting
	 */
	private function db_migrate(array $databaseSetting)
	{
		$this->scriptArgument->log('!!!!!!!!!!db_migrate!!!!!!!!!!');

		// SQLite を使うのは決定事項である！
		$filePath = $databaseSetting['connection'];
		$dbVersion = -1;
		if (file_exists($filePath)) {
			$checkPdo = $this->createConnection($databaseSetting);
			$checkCountStatement = $checkPdo->query("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
			if (0 < $checkCountStatement->fetchColumn()) {
				$versionStatement = $checkPdo->query("select version from database_version");
				$versionNumber = $versionStatement->fetchColumn();
				if ($versionNumber !== false) {
					$dbVersion = (int)$versionNumber;
				}
				$versionStatement = null;
			}
			$checkCountStatement = null;
			$checkPdo = null;
		}

		$db_migrates = [
			'db_migrates_0000',
		];

		$newVersion = 0;
		$pdo = $this->createConnection($databaseSetting);
		$pdo->exec('PRAGMA foreign_keys = OFF;');
		for ($i = $dbVersion + 1; $i < count($db_migrates); $i++) {
			$this->scriptArgument->log('DB: ' . $db_migrates[$i]);
			call_user_func(array($this, $db_migrates[$i]), $pdo);
			$newVersion = $i;
		}

		$this->db_migrates_9999($pdo, $dbVersion, $newVersion);

		$pdo->exec('PRAGMA foreign_keys = ON;');
	}

	// 管理ユーザー admin/admin が作られるので公開時は速やかに破棄 or 変更すること
	private function db_migrates_0000(PDO $pdo)
	{
		//TODO: 全削除処理
		$tablesStatement = $pdo->query("select sqlite_master.name as name from sqlite_master where sqlite_master.type='table' and sqlite_master.name <> 'sqlite_sequence'");
		$tableNameRows = $tablesStatement->fetchAll();

		foreach ($tableNameRows as $tableNameRow) {
			$tableName = $tableNameRow['name'];
			$pdo->exec("drop table $tableName");
		}
		$tableNameRows = null;
		$tablesStatement = null;

		//TODO: 暗号化とかとか
		$userId = '00000000-0000-4000-0000-000000000000';
		$loginId = 'setup_' . date('YmdHis');
		$rawPassword = bin2hex(openssl_random_pseudo_bytes(4));
		$encPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

		$pdo->exec(
			<<<SQL

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
		[login_id] text not null unique, -- ログインID
		[level] text not null, -- ユーザーレベル(権限てきな)
		[state] text not null, -- 状態
		[name] text not null, -- 名前
		[email] text not null, -- メールアドレス(暗号化)
		[mark_email] integer not null, -- 絞り込み用メールアドレス(ハッシュ:fnv)
		[website] text not null, -- Webサイト
		[note] text not null, -- 管理者用メモ
		primary key([user_id])
	)
;

create table
	[user_authentications] -- ユーザー認証情報
	(
		[user_id] text not null, -- ユーザーID
		[generate_password] text not null, -- 自動生成パスワード(ハッシュ) 空白の可能性あり(セットアップ・管理者等)
		[current_password] text not null, -- 現在パスワード(ハッシュ)
		primary key([user_id]),
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
		[mark_email] integer not null, -- 絞り込み用メールアドレス(ハッシュ:fnv)
		[token] text not null, -- トークン
		[email] text not null, -- メールアドレス(暗号化)
		[timestamp] text not null, -- トークン発行日時(UTC)
		[ip_address] text not null, -- クライアントIPアドレス
		[user_agent] text not null -- クライアントUA
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
		[note]
	)
	values
	(
		'$userId',
		'$loginId',
		'setup',
		'enabled',
		'setup user',
		'',
		0,
		'',
		''
	)
;

insert into
	[user_authentications]
	(
		[user_id],
		[generate_password],
		[current_password]
	)
	values
	(
		'$userId',
		'',
		'$encPassword'
	)
;

SQL
		);

		$this->scriptArgument->log('SETTUP LOG');
		$this->scriptArgument->log([
			'userId' => $userId,
			'loginId' => $loginId,
			'password' => $rawPassword,
		]);
	}

	private function db_migrates_9999(PDO $pdo, int $oldVersion, int $newVersion)
	{
		if ($oldVersion === $newVersion) {
			return;
		}

		if ($oldVersion === -1) {
			$pdo->exec("insert into database_version(version) values (${newVersion})");
		} else {
			$pdo->exec("update database_version set version = ${newVersion}");
		}
	}
}

function before_update(\Deploy\ScriptArgument $scriptArgument)
{
	$deploy = new DeployScript($scriptArgument);
	$deploy->before();
}


function after_update(\Deploy\ScriptArgument $scriptArgument)
{
	$deploy = new DeployScript($scriptArgument);
	$deploy->after();
}
