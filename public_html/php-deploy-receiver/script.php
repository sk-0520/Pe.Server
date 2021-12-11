<?php

function before_update($scriptArgument)
{
	$scriptArgument->log('before script');

	// //TODO: キャッシュの破棄
	// $removeDirs = [
	// 	$scriptArgument->joinPath($scriptArgument->publicDirectoryPath, 'data', 'temp', 'views', 'c'),
	// ];
}


function after_update($scriptArgument)
{
	$scriptArgument->log('after script');
	// //TODO: DBのマイグレーション処理
}
