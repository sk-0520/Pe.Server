<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use DateInterval;
use DateTime;
use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Binary;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Utc;

class AdministratorApiDeployLogic extends ApiLogicBase
{
	#region define

	const MODE_STARTUP = 'startup';
	const MODE_UPLOAD = 'upload';
	const MODE_PREPARE = 'prepare';
	const MODE_UPDATE = 'update';

	const FILE_PROGRESS = 'deploy.json';
	const ENABLED_RANGE_TIME = 'PT5M';

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $appConfig)
	{
		parent::__construct($parameter);
	}

	#region function

	private function getProgressFilePath(): string
	{
		return Path::combine($this->appConfig->baseDirectoryPath, 'data/temp', self::FILE_PROGRESS);
	}

	private function getProgressSetting(): LocalProgressSetting
	{
		$path = $this->getProgressFilePath();
		$binary = File::readContent($path);
		$jsonSerializer = new JsonSerializer();
		/** @var LocalProgressSetting */
		return $jsonSerializer->load($binary);
	}

	private function executeStartup(): array
	{
		throw new NotImplementedException();
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
		$modeIsEnabled = ArrayUtility::in([
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
				$this->logger->error('進捗状況 時間制限超過: $limit {0} < $current {1}', $limit, $current);
				throw new Exception('$limit: ' . $limit);
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
