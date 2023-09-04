<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\Api\AdministratorApi\AdministratorApiDeployLogic;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Timer;

class ManagementClearDeployProgressLogic extends PageLogicBase
{
	public function __construct(
		LogicParameter $parameter,
		private AppConfiguration $appConfig,
	) {
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$stopwatch =  Timer::startNew();

		$progressFilePath = Path::combine($this->appConfig->setting->cache->deploy, AdministratorApiDeployLogic::PROGRESS_FILE_NAME);
		$this->logger->debug('$progressFilePath: {0}', $progressFilePath);
		$existsProgressFilePath = File::exists($progressFilePath);
		if($existsProgressFilePath) {
			File::removeFile($progressFilePath);
		}

		$stopwatch->stop();

		if ($existsProgressFilePath) {
			$this->addTemporaryMessage('デプロイ進捗ファイル破棄完了: ' . $stopwatch->toString());
		} else {
			$this->addTemporaryMessage('デプロイ進捗ファイルなし: ' . $stopwatch->toString());
		}
	}
}
