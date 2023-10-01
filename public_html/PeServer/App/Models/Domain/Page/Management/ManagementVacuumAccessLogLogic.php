<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Domain\AccessLogManager;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Timer;

class ManagementVacuumAccessLogLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AccessLogManager $accessLogManager, private AppDatabaseCache $dbCache)
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

		$this->accessLogManager->vacuum();

		$stopwatch->stop();

		$this->addTemporaryMessage('アクセスログ整理完了: ' . $stopwatch->toString());
	}
}
