<?php

namespace PeServer\App\Controllers;

use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\Core\Mvc\ControllerBase;

abstract class DomainControllerBase extends ControllerBase
{
	protected function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
