<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ControllerArguments;
use \PeServer\App\Controllers\Api\ApiControllerBase;

class HelloController extends ApiControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
