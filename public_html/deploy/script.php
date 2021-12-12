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
			mkdir($backupDirectoryPath, 077, true);
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
		$pdo = new PDO('sqlite:' . $databaseSetting['connection']);
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
		$dbVersion = 0;
		if (file_exists($filePath)) {
			$checkPdo = $this->createConnection($databaseSetting);
			$checkCountStatement = $checkPdo->query("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
			if (0 < $checkCountStatement->fetchColumn()) {
				$versionStatement = $checkPdo->query("select version from database_version");
				$dbVersion = $versionStatement->fetchColumn();
			}
		}

		$db_migrates = [
			'db_migrates_0',
		];

		$pdo = $this->createConnection($databaseSetting);
		for ($i = $dbVersion; $i < count($db_migrates); $i++) {
			$this->scriptArgument->log('DB: ' . $db_migrates[$i]);
			call_user_func(array($this, $db_migrates[$i]), $pdo);
		}
	}

	private function db_migrates_0(PDO $pdo)
	{
		//TODO: 全削除処理

		$pdo->exec(
			<<<SQL
			create table
				[database_version]
				(
					[version] integer not null
				)
			;

			create table [users] (
				[user_id]    text not null,
				[user_name]  text not null,
				[user_mail_address] text not null,
				[user_website] text not null,
				[user_note] text not null,
				primary key([user_id])
			);
SQL
		);
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
