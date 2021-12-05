<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\ILogger;

class LogicParameter
{
	public $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}
}
