<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AppMailer;
use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Environment;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mail\Attachment;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mvc\ILogicFactory;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Utc;
use ZipArchive;

class AppArchiver
{
	#region define

	/** 保持ファイル数。 半年分と適当にやる分 180 個, 理屈の上で 1 年分じゃないぞー */
	private const MAX_COUNT = 183 + 180;

	#endregion

	#region property

	private ILogger $logger;

	#endregion

	public function __construct(
		private Environment $environment,
		private AppConfiguration $config,
		private AppMailer $mailer,
		ILoggerFactory $loggerFactory,
	) {
		$this->logger = $loggerFactory->createLogger($this);
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
		$revision = $this->environment->getRevision();
		$fileName = Utc::create()->format('Y-m-d\_His') . "_{$revision}.zip";
		$filePath = Path::combine($this->getDirectory(), $fileName);
		Directory::createParentDirectoryIfNotExists($filePath);

		$zipArchive = new ZipArchive();
		$zipArchive->open($filePath, ZipArchive::CREATE | ZipArchive::EXCL);

		//DB保存
		$databasePath = AppDatabaseConnection::getSqliteFilePath($this->config->setting->persistence->default->connection);
		$zipArchive->addFile($databasePath, Path::getFileName($databasePath));
		// 設定ファイル保存
		$settingName = Path::setEnvironmentName('setting.json', $this->environment->get());
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

	public function sendLatestArchive(string $subject, bool $ignoreError): void
	{
		$backupFiles = $this->getFiles();
		if (Arr::isNullOrEmpty($backupFiles)) {
			if ($ignoreError) {
				return;
			}
			throw new InvalidOperationException();
		}

		$backupFilePath = $backupFiles[count($backupFiles) - 1];
		$backupFileName = Path::getFileName($backupFilePath);
		assert(!Text::isNullOrWhiteSpace($backupFileName));

		$this->mailer->subject = "[Backup] {$subject} {$backupFileName}";
		$this->mailer->setMessage(new EmailMessage(
			"バックアップ実施"
		));
		$this->mailer->attachments[] = new Attachment(
			$backupFileName,
			File::readContent($backupFilePath)
		);

		foreach ($this->config->setting->config->address->notify->maintenance as $email) {
			$this->mailer->toAddresses = [
				new EmailAddress($email),
			];

			try {
				$this->mailer->send();
			} catch (Exception $ex) {
				$this->logger->error($ex);
				if (!$ignoreError) {
					throw $ex;
				}
			}
		}
	}
}
