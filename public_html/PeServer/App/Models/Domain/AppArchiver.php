<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\Core\Archiver;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Environment;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Utc;
use ZipArchive;

class AppArchiver
{
	/** 保持ファイル数。 */
	private const MAX_COUNT = 180;

	public function __construct(
		private AppConfiguration $config
	) {
		//NOP
	}

	public function getDirectory(): string
	{
		return $this->config->setting->cache->backup;
	}

	/**
	 * アーカイブファイル一覧の取得。
	 *
	 * @return string[]
	 */
	public function getFiles(): array
	{
		return Directory::find($this->getDirectory(), '*.zip');
	}

	public function backup(): int
	{
		$revision = Environment::getRevision();
		$fileName = Utc::create()->format('Y-m-d\_His') . "_{$revision}.zip";
		$filePath = Path::combine($this->getDirectory(), $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);

		$zipArchive = new ZipArchive();
		$zipArchive->open($filePath, ZipArchive::CREATE | ZipArchive::EXCL);

		//DB保存
		$databasePath = AppDatabaseConnection::getSqliteFilePath($this->config->setting->persistence->default->connection);
		$zipArchive->addFile($databasePath, Path::getFileName($databasePath));
		// 設定ファイル保存
		$settingName = Path::setEnvironmentName('setting.json', Environment::get());
		$settingPath = Path::combine($this->config->settingDirectoryPath, $settingName);
		$zipArchive->addFile($settingPath, $settingName);

		$zipArchive->close();

		return File::getFileSize($filePath);
	}

	public function rotate(): void
	{
		$backupFiles = $this->getFiles();
		$logCount = Arr::getCount($backupFiles);
		if ($logCount <= self::MAX_COUNT) {
			return;
		}

		$length = $logCount - self::MAX_COUNT;
		for ($i = 0; $i < $length; $i++) {
			File::removeFile($backupFiles[$i]);
		}
	}
}
