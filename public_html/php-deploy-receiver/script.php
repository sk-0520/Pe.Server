<?php

class Deploy
{
	private $scriptArgument;

	public function __construct(ScriptArgument $scriptArgument)
	{
		$this->scriptArgument = $scriptArgument;
	}

	public function before(): void
	{
		$this->scriptArgument->log('before script');

		// キャッシュの破棄
		$removeDirs = [
			$this->scriptArgument->joinPath($this->scriptArgument->publicDirectoryPath, 'data', 'temp', 'views', 'c'),
		];

		foreach ($removeDirs as $removeDir) {
			$this->scriptArgument->log('remove: ' . $removeDir);
			$this->scriptArgument->cleanupDirectory($removeDir);
		}

		// DBバックアップ
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
