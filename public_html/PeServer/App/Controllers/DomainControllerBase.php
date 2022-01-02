<?php

namespace PeServer\App\Controllers;

use PeServer\Core\Mvc\ControllerArgument;
use PeServer\Core\Mvc\ControllerBase;

abstract class DomainControllerBase extends ControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}
}
