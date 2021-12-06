<?php

declare(strict_types=1);

namespace PeServer\Core;

use \PeServer\Core\ILogger;

class ControllerArguments
{
	public $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}
}
