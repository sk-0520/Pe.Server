<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\App\Models\Domains\LogicParameter;

abstract class LogicBase
{
	protected $logger;

	protected function __construct(LogicParameter $parameter)
	{
		$this->logger = $parameter->logger;
	}
}
