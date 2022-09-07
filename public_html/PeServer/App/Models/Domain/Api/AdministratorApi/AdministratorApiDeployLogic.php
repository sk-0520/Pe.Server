<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use \DateInterval;
use \DateTime;
use \Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Binary;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Serialization\BuiltinSerializer;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Utc;

class AdministratorApiDeployLogic extends ApiLogicBase
{
	#region define

	private const MODE_STARTUP = 'startup';
	private const MODE_UPLOAD = 'upload';
	private const MODE_PREPARE = 'prepare';
	private const MODE_UPDATE = 'update';

	private const FILE_PROGRESS = 'deploy.dat';
	private const ENABLED_RANGE_TIME = 'PT5M';

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $appConfig)
	{
		parent::__construct($parameter);
	}

	#region function

	private function getProgressFilePath(): string
	{
		return Path::combine(Directory::getTemporaryDirectory(), self::FILE_PROGRESS);
	}

	private function getExpandDirectoryPath(): string
	{
		return Path::combine(Directory::getTemporaryDirectory(), 'deploy');
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
		File::writeContent($path, $binary);
	}

	private function executeStartup(): array
	{
		$setting = new LocalProgressSetting();
		$setting->mode = self::MODE_STARTUP;
		$setting->startupTime = Utc::createDateTime();

		$dirPath = $this->getExpandDirectoryPath();
		if (Directory::exists($dirPath)) {
			Directory::removeDirectory($dirPath, true);
		}

		$this->setProgressSetting($setting);

		return [];
	}

	private function executeUpload(): array
	{
		throw new NotImplementedException();
	}

	private function executePrepare(): array
	{
		throw new NotImplementedException();
	}

	private function executeUpdate(): array
	{
		throw new NotImplementedException();
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
			if ($current < $limit) {
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
		} else if ($mode !== self::MODE_STARTUP) {
			throw new Exception('$mode: ' . $mode . ', not found progress');
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
		};

		$this->setResponseJson(ResponseJson::success($result));
	}

	#endregion
}

class LocalProgressSetting
{
	#region variable

	public DateTime $startupTime;
	public string $mode;

	#endregion
}
