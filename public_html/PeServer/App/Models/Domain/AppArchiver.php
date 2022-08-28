<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Archiver;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Environment;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Utc;
use ZipArchive;

class AppArchiver
{
	private const MAX_COUNT = 500;

	public function __construct(
		private AppConfiguration $config
	) {
		//NONE
	}

	public function backup(): int
	{
		$revision = Environment::getRevision();
		$fileName = Utc::create()->format('Y-m-d\_His') . "_{$revision}.zip";
		$filePath = Path::combine($this->config->setting->cache->backup, $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);

		$zipArchive = new ZipArchive();
		$zipArchive->open($filePath, ZipArchive::CREATE | ZipArchive::EXCL);

		//DB保存
		$connectionPath = $this->config->setting->persistence->default->connection;
		$databasePath = Text::split($connectionPath, ':', 2)[1];
		$zipArchive->addFile($databasePath, Path::getFileName($databasePath));

		$zipArchive->close();

		return File::getFileSize($filePath);
	}

	public function rotate(): void
	{
		$backupFiles = Directory::find($this->config->setting->cache->backup, '*.zip');
		$logCount = ArrayUtility::getCount($backupFiles);
		if ($logCount <= self::MAX_COUNT) {
			return;
		}

		$length = $logCount - self::MAX_COUNT;
		for ($i = 0; $i < $length; $i++) {
			File::removeFile($backupFiles[$i]);
		}
	}
}
