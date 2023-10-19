<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Mvc\ControllerArgument;

class AccountApiController extends ApiControllerBase
{
	public function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}
}
