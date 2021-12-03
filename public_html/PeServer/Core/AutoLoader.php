<?php declare(strict_types=1);

namespace PeServer\Core;

function registerAutoLoader(array $baseDirectoryPaths)
{
	spl_autoload_register(function(string $className) use($baseDirectoryPaths) {
		foreach($baseDirectoryPaths as $baseDirectoryPath) {
			$fileBasePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
			$filePath = $baseDirectoryPath . DIRECTORY_SEPARATOR . $fileBasePath . '.php';

			if(file_exists($filePath)) {
				require_once $filePath;
			}
		}
	});
}
