<?php

class Deploy
{
	private $scriptArgument;
	private $fileTimestampName;

	public function __construct(ScriptArgument $scriptArgument)
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
		if(!file_exists($backupDirectoryPath)) {
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
		//TODO: DBのマイグレーション処理
	}
}

function before_update(ScriptArgument $scriptArgument)
{
	$deploy = new Deploy($scriptArgument);
	$deploy->before();
}


function after_update(ScriptArgument $scriptArgument)
{
	$deploy = new Deploy($scriptArgument);
	$deploy->after();
}
