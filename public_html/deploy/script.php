<?php

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

		// キャッシュの破棄
		$removeDirs = [
			$this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data', 'temp', 'views', 'c'),
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
			'driver' => 'sqlite3',
			'connection' => $this->scriptArgument->joinPath($this->getAppDirectoryPath(), 'data/data.sqlite3'),
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
		$pdo = new PDO('sqlite:' . $databaseSetting['connection']/*, null, null, [
			PDO::ATTR_PERSISTENT => true,
		]*/);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
		$tablesStatement = $pdo->query("select sqlite_master.name from sqlite_master where sqlite_master.type='table' and sqlite_master.name <> 'sqlite_sequence'");
		$tableNameRows = $tablesStatement->fetchAll();

		foreach ($tableNameRows as $tableNameRow) {
			$tableName = $tableNameRow[0];
			$pdo->exec("drop table $tableName");
		}
		$tableNameRows = null;
		$tablesStatement = null;

		//TODO: 暗号化とかとか
		$userId = '00000000-0000-4000-0000-000000000000';
		$loginId = 'init';
		$password = bin2hex(openssl_random_pseudo_bytes(4));

		$pdo->exec(
			<<<SQL
create table
	[database_version]
	(
		[version] integer not null
	)
;

create table
	[users]
	(
		[user_id] text not null,
		[login_id] text not null unique,
		[level] text not null,
		[state] text not null,
		[name] text not null,
		[mail_address] text not null,
		[website] text not null,
		[note] text not null,
		primary key([user_id])
	)
;

create table
	[user_authentications]
	(
		[user_id] text not null,
		[default_password] text not null,
		[current_password] text not null,
		primary key([user_id]),
		foreign key ([user_id]) references users([user_id])
	)
;

create table
	[user_audit_logs]
	(
		[sequence] integer not null,
		[user_id] text not null,
		[timestamp] datetime not null,
		[event] text not null,
		[ip_address] text not null,
		[user_agent] text not null,
		primary key([sequence] autoincrement),
		foreign key ([user_id]) references users([user_id])
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
		[mail_address],
		[website],
		[note]
	)
	values
	(
		'$userId',
		'$loginId',
		'init',
		'enabled',
		'initial-user',
		'init@localhost',
		'http://localhost',
		''
	)
;

insert into
	[user_authentications]
	(
		[user_id],
		[default_password],
		[current_password]
	)
	values
	(
		'$userId',
		'',
		'$password'
	)
;

SQL
		);
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
