<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use DateInterval;
use DateTime;
use Exception;
use ZipArchive;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Migration\AppMigrationRunnerFactory;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Serialization\BuiltinSerializer;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Utc;

class AdministratorApiDeployLogic extends ApiLogicBase
{
	#region define

	private const MODE_STARTUP = 'startup';
	private const MODE_UPLOAD = 'upload';
	private const MODE_PREPARE = 'prepare';
	private const MODE_UPDATE = 'update';

	public const PROGRESS_FILE_NAME = 'deploy.dat';
	private const ARCHIVE_FILE_NAME = 'deploy.zip';
	private const ENABLED_RANGE_TIME = 'P1M';

	#endregion

	public function __construct(
		LogicParameter $parameter,
		private ProgramContext $programContext,
		private AppConfiguration $appConfig,
		private AppArchiver $appArchiver,
		private AppMigrationRunnerFactory $migrationRunnerFactory
	) {
		parent::__construct($parameter);
	}

	#region function

	private function getProgressFilePath(): string
	{
		return Path::combine($this->appConfig->setting->cache->deploy, self::PROGRESS_FILE_NAME);
	}

	private function getArchiveFilePath(): string
	{
		return Path::combine($this->appConfig->setting->cache->deploy, self::ARCHIVE_FILE_NAME);
	}

	private function getUploadDirectoryPath(): string
	{
		return Path::combine($this->appConfig->setting->cache->deploy, 'upload');
	}

	private function getExpandDirectoryPath(): string
	{
		return Path::combine($this->appConfig->setting->cache->deploy, 'expand');
	}

	private function getProgressSetting(): LocalProgressSetting
	{
		$path = $this->getProgressFilePath();
		$binary = File::readContent($path);
		$serializer = new BuiltinSerializer();
		/** @var LocalProgressSetting */
		return $serializer->load($binary);
	}

	private function setProgressSetting(LocalProgressSetting $setting): void
	{
		$path = $this->getProgressFilePath();
		$serializer = new BuiltinSerializer();
		$binary = $serializer->save($setting);
		Directory::createParentDirectoryIfNotExists($path);
		File::writeContent($path, $binary);
	}

	/**
	 * [デプロイ] 開始の合図。
	 *
	 * @return array<string,string>
	 */
	private function executeStartup(): array
	{
		$setting = new LocalProgressSetting();
		$setting->mode = self::MODE_STARTUP;
		$setting->uploadedCount = 0;
		$setting->startupTime = Utc::createDateTime();

		$dirs = [
			$this->getUploadDirectoryPath(),
			$this->getExpandDirectoryPath(),
		];
		foreach ($dirs as $dir) {
			if (Directory::exists($dir)) {
				Directory::removeDirectory($dir, true);
			}
		}

		$files = [
			$this->getArchiveFilePath(),
		];
		foreach ($files as $file) {
			if (File::exists($file)) {
				File::removeFile($file);
			}
		}

		$this->setProgressSetting($setting);

		return [];
	}

	/**
	 * [デプロイ] アップロード処理。
	 *
	 * @return array<string,string>
	 */
	private function executeUpload(): array
	{
		$sequence = $this->getRequest('sequence');
		if (Text::isNullOrWhiteSpace($sequence)) {
			throw new InvalidOperationException('$sequence');
		}

		$file = $this->getFile('file');
		$fileName = "upload-$sequence.dat";
		$filePath = Path::combine($this->getUploadDirectoryPath(), $fileName);
		$file->move($filePath);

		$setting = $this->getProgressSetting();
		$setting->mode = self::MODE_UPLOAD;
		$setting->uploadedCount += 1;
		$this->setProgressSetting($setting);

		return [
			'sequence' => $sequence,
		];
	}

	/**
	 * [デプロイ] 展開処理。
	 *
	 * @return array<string,string>
	 */
	private function executePrepare(): array
	{
		$archiveFilePath = $this->getArchiveFilePath();
		File::removeFileIfExists($archiveFilePath);
		File::createEmptyFileIfNotExists($archiveFilePath);

		$setting = $this->getProgressSetting();
		for ($i = 0; $i < $setting->uploadedCount; $i++) {
			$uploadFileName = "upload-$i.dat";
			$uploadFilePath = Path::combine($this->getUploadDirectoryPath(), $uploadFileName);
			$content = File::readContent($uploadFilePath);
			File::appendContent($archiveFilePath, $content);
		}

		$archiveFilePath = $this->getArchiveFilePath();
		if (!File::exists($archiveFilePath)) {
			throw new InvalidOperationException($archiveFilePath);
		}

		$expandDirPath = $this->getExpandDirectoryPath();
		if (Directory::exists($expandDirPath)) {
			Directory::removeDirectory($expandDirPath, true);
		}
		Directory::createDirectory($expandDirPath);

		// 展開
		$archiveFilePath = $this->getArchiveFilePath();
		$zip = new ZipArchive();
		$zip->open($archiveFilePath);
		$expandDirPath = $this->getExpandDirectoryPath();
		$zip->extractTo($expandDirPath);
		$zip->close();

		$expandFilePaths = Directory::getFiles($expandDirPath, true);
		foreach ($expandFilePaths as $expandFilePath) {
			$this->logger->info('{0}: {1}', $expandFilePath, File::getFileSize($expandFilePath));
		}

		$setting->mode = self::MODE_PREPARE;
		$this->setProgressSetting($setting);

		return [
			'count' => (string)$setting->uploadedCount,
			'size' => (string)File::getFileSize($archiveFilePath),
		];
	}

	/**
	 * [デプロイ] 最終処理。
	 *
	 * @return array<string,string>
	 */
	private function executeUpdate(): array
	{
		$this->logger->info('各種データバックアップ');
		$this->appArchiver->backup();
		$this->appArchiver->rotate();

		// 今のソースでDB開いとく(使わんけど)
		$database = $this->openDatabase();

		$this->logger->info('展開ファイルを公開ディレクトリに配置');
		$dirs = [];
		$expandDirPath = $this->getExpandDirectoryPath();
		$expandFilePaths = Directory::getFiles($expandDirPath, true);
		foreach ($expandFilePaths as $expandFilePath) {
			$basePath = Text::substring($expandFilePath, Text::getLength($expandDirPath) + 1);
			$toPath = Path::combine($this->programContext->rootDirectory, $basePath);

			//$this->logger->info('UPDATE: {0}', $toPath);

			$parentPath = Path::getDirectoryPath($toPath);
			if (!isset($dirs[$parentPath])) {
				Directory::createDirectoryIfNotExists($parentPath);
				$dirs[$parentPath] = true;
			}
			File::copy($expandFilePath, $toPath);
		}

		$cronShellDir = Path::combine($this->programContext->applicationDirectory, "cron");
		$cronShellFiles = Directory::getFiles($cronShellDir, true);
		foreach ($cronShellFiles as $cronShellFile) {
			if (Path::getFileExtension($cronShellFile) === 'sh') {
				$this->logger->info('権限変更: {0}', $cronShellFile);
				IOUtility::changePermission($cronShellFile, 0744);
			}
		}

		$this->logger->info('各種マイグレーション実施');
		$setupRunner = $this->migrationRunnerFactory->create();

		$setupRunner->execute();

		$this->logger->info('デプロイ進捗ファイル破棄');
		$progressFilePath = Path::combine($this->appConfig->setting->cache->deploy, AdministratorApiDeployLogic::PROGRESS_FILE_NAME);
		File::removeFile($progressFilePath);

		return [
			'success' => (string)true,
		];
	}

	#endregion

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$mode = $this->getRequest('mode');
		$modeIsEnabled = Arr::in([
			self::MODE_STARTUP,
			self::MODE_UPLOAD,
			self::MODE_PREPARE,
			self::MODE_UPDATE,
		], $mode);

		if (!$modeIsEnabled) {
			throw new Exception('$mode: ' . $mode);
		}

		if (File::exists($this->getProgressFilePath())) {
			$setting = $this->getProgressSetting();
			$current = Utc::createDateTime();
			$limit = $setting->startupTime->add(new DateInterval(self::ENABLED_RANGE_TIME));
			if ($limit < $current) {
				$this->logger->error('進捗状況 時間制限: $current {0} < $limit {1}', Utc::toString($current), Utc::toString($limit));
				throw new Exception('$limit: ' . Utc::toString($limit));
			}

			switch ($mode) {
				case self::MODE_STARTUP:
					$this->logger->info('進捗ファイルは無視');
					break;

				case self::MODE_UPLOAD:
					if (!($setting->mode === self::MODE_STARTUP || $setting->mode === self::MODE_UPLOAD)) {
						$this->logger->error('進捗状況不整合: {0}', $setting->mode);
						throw new Exception('$setting->mode: ' . $setting->mode);
					}
					break;

				case self::MODE_PREPARE:
					if ($setting->mode !== self::MODE_UPLOAD) {
						$this->logger->error('進捗状況不整合: {0}', $setting->mode);
						throw new Exception('$setting->mode: ' . $setting->mode);
					}
					break;

				case self::MODE_UPDATE:
					if ($setting->mode !== self::MODE_PREPARE) {
						$this->logger->error('進捗状況不整合: {0}', $setting->mode);
						throw new Exception('$setting->mode: ' . $setting->mode);
					}
					break;

				default:
					throw new NotImplementedException();
			}
		} elseif ($mode !== self::MODE_STARTUP) {
			throw new Exception('$mode: ' . $mode . ', not found progress -> ' . $this->getProgressFilePath());
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$mode = $this->getRequest('mode');

		$result = match ($mode) {
			self::MODE_STARTUP => $this->executeStartup(),
			self::MODE_UPLOAD => $this->executeUpload(),
			self::MODE_PREPARE => $this->executePrepare(),
			self::MODE_UPDATE => $this->executeUpdate(),
			default => throw new Exception()
		};

		$this->setResponseJson(ResponseJson::success($result));
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalProgressSetting
{
	#region variable

	public DateTime $startupTime;
	public string $mode;
	public int $uploadedCount;

	#endregion
}
