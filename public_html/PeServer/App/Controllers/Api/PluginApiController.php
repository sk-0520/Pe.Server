<?php

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\Api\ApiControllerBase;


class PluginApiController extends ApiControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}
}
