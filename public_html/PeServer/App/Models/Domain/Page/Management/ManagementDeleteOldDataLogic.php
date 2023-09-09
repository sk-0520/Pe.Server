<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Timer;

class ManagementDeleteOldDataLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$stopwatch =  Timer::startNew();

		// DO

		$stopwatch->stop();

		$this->addTemporaryMessage('不要データ削除: ' . $stopwatch->toString());
	}
}
