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

	/**
	 * @param {driver:string,connection:string,user:string,password:string} $databaseSetting
	 */
	private function db_migrate(array $databaseSetting)
	{
		$this->scriptArgument->log('!!!!!!!!!!db_migrate!!!!!!!!!!');
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
