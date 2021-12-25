<?php

namespace PeServer\App\Controllers\Page;

use \PeServer\Core\Mvc\ControllerBase;
use \PeServer\Core\Mvc\ControllerArguments;
use \PeServer\App\Controllers\DomainControllerBase;

abstract class PageControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
