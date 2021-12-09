<?php

declare(strict_types=1);

function before_update($scriptArgument)
{
	//TODO: キャッシュの破棄
	$removeDirs = [
		//joinPath($scriptArgument->publicDirectoryPath, 'data', 'temp', 'views', 'c'),
	];
}


function after_update($scriptArgument)
{
	//TODO: DBのマイグレーション処理
}
