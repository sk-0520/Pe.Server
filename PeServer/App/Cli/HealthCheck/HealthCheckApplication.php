<?php

declare(strict_types=1);

namespace PeServer\App\Cli\HealthCheck;

use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Log\ILoggerFactory;

class HealthCheckApplication extends CliApplicationBase
{
	public function __construct(public HealthCheckParameter $parameter, ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region CliApplicationBase

	public function execute(): void
	{
		$this->logger->info("echo {0}", $this->parameter->echo);
	}

	#endregion
}
