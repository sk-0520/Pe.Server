<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;

class LogicParameter
{
	public $logger;
	public $request;

	public function __construct(ActionRequest $request, ILogger $logger)
	{
		$this->request = $request;
		$this->logger = $logger;
	}
}
