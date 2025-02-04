<?php

declare(strict_types=1);

namespace PeServer\App\Cli\HealthCheck;

use Error;
use PeServer\App\Cli\AppApplicationBase;
use PeServer\App\Models\AppDatabaseConnection;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\Core\Environment;
use PeServer\Core\Log\ILoggerFactory;

class HealthCheckApplication extends AppApplicationBase
{
	public function __construct(public HealthCheckParameter $parameter, private Environment $environment, ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region AppApplicationBase

	public function executeImpl(): void
	{
		$this->logger->info("input {0}", $this->parameter->echo);

		$this->logger->info("app version: {0}", $this->environment->getRevision());

		$database = $this->openDatabase();
		$peSettingEntityDao = new PeSettingEntityDao($database);
		$peVersion = $peSettingEntityDao->selectPeSettingVersion();
		$this->logger->info("pe version: {0}", $peVersion);

		throw new Error("error");
	}

	#endregion
}
