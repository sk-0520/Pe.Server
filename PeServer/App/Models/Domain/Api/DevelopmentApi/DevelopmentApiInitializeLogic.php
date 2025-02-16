<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\DevelopmentApi;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\App\Models\Setup\SetupRunner;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\ILogicFactory;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;

class DevelopmentApiInitializeLogic extends ApiLogicBase
{
	public function __construct(
		LogicParameter $parameter,
		private AppConfiguration $config,
		private IDatabaseConnection $connection,
		private ILoggerFactory $loggerFactory
	) {
		parent::__construct($parameter);
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$setupRunner = new SetupRunner(
			$this->connection,
			$this->config,
			$this->loggerFactory
		);

		$setupRunner->execute();

		$this->setResponseJson(ResponseJson::success([
			'success' => true,
		]));
	}

	#endregion
}
