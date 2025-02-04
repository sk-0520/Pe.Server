<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Stopwatch;
use Throwable;

class ManagementBackupLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppArchiver $appArchiver)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$this->appArchiver->backup();
		$this->appArchiver->rotate();
		try {
			$this->appArchiver->sendLatestArchive(ManagementBackupLogic::class, false);
			$this->addTemporaryMessage('バックアップ完了');
		} catch (Throwable $ex) {
			$this->addTemporaryMessage('バックアップ中にエラーあり');
			$this->addTemporaryMessage($ex->getMessage());
		}
	}
}
