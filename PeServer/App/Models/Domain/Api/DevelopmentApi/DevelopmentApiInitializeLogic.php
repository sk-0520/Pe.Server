<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\DevelopmentApi;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Migration\AppMigrationRunnerFactory;
use PeServer\App\Models\ResponseJson;
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
		private AppMigrationRunnerFactory $migrationRunnerFactory
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

		$setupRunner = $this->migrationRunnerFactory->create();

		$setupRunner->execute();

		$this->setResponseJson(ResponseJson::success([
			'success' => true,
		]));
	}

	#endregion
}
