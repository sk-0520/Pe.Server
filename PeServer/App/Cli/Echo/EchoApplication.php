<?php

declare(strict_types=1);

namespace PeServer\App\Cli\Echo;

use PeServer\App\Cli\CliApplicationBase;

class EchoApplication extends CliApplicationBase
{
	public function __construct(private EchoParameter $parameter)
	{
		//NOP
	}

	#region CliApplicationBase

	public function execute(): void
	{
		$this->logger->info("ECHO");
	}

	#endregion
}
