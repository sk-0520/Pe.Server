<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\DomainControllerBase;

final class ErrorController extends PageControllerBase
{
	protected function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
