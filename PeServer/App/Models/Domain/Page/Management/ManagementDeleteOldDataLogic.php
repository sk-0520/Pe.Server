<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\AppEraser;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Stopwatch;

class ManagementDeleteOldDataLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppEraser $appEraser)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$stopwatch =  Stopwatch::startNew();

		$this->appEraser->execute();

		$stopwatch->stop();

		$this->addTemporaryMessage('不要データ削除: ' . $stopwatch->toString());
	}
}
