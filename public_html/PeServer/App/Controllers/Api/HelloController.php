<?php

namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ControllerArguments;

class HelloController extends ApiControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
