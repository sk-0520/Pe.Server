<?php

declare(strict_types=1);

function joinPath(string $basePath, string ...$addPaths): string
{
	$paths = array_merge([$basePath], $addPaths);

	$joinedPath = implode(DIRECTORY_SEPARATOR, $paths);
	return $joinedPath;
}

function before_update(array $scriptData)
{
	//TODO: キャッシュの破棄
	$removeDirs = [
		joinPath($scriptData['public'], 'data', 'temp', 'views', 'c'),
	];
}


function after_update(array $scriptData)
{
	//TODO: DBのマイグレーション処理
}
