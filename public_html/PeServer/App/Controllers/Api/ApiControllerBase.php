<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\DomainControllerBase;

abstract class ApiControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
