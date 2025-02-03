<?php

declare(strict_types=1);

namespace PeServer\App\Cli\Echo;

use PeServer\Core\Cli\CliApplicationBase;
use PeServer\Core\Log\ILoggerFactory;

class EchoApplication extends CliApplicationBase
{
	public function __construct(public EchoParameter $parameter, ILoggerFactory $loggerFactory)
	{
		parent::__construct($loggerFactory);
	}

	#region CliApplicationBase

	public function execute(): void
	{
		$this->logger->info("ECHO {0}", $this->parameter->input);
	}

	#endregion
}
