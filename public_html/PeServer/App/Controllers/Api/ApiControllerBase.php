<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\ControllerBase;

abstract class ApiControllerBase extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
