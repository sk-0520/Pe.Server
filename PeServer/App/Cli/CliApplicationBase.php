<?php

declare(strict_types=1);

namespace PeServer\App\Cli;

use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\ILoggerFactory;

abstract class CliApplicationBase
{
	#region variable

	protected ILogger $logger;
	public int $exitCode = 0;

	#endregion

	public function __construct(protected ILoggerFactory $loggerFactory)
	{
		$this->logger = $loggerFactory->createLogger($this);
	}

	#region function

	abstract public function execute(): void;

	#endregion
}
