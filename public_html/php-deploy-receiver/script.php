<?php

declare(strict_types=1);

require __DIR__ . '/php-deploy-receiver.php';


function before_update(ScriptArgument $scriptArgument)
{
	$scriptArgument->log('before script');

	//TODO: キャッシュの破棄
	$removeDirs = [
		$scriptArgument->joinPath($scriptArgument->publicDirectoryPath, 'data', 'temp', 'views', 'c'),
	];
}


function after_update(ScriptArgument $scriptArgument)
{
	$scriptArgument->log('after script');
	//TODO: DBのマイグレーション処理
}
