<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArgument;
use \PeServer\App\Controllers\DomainControllerBase;

final class ErrorController extends PageControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}
}
