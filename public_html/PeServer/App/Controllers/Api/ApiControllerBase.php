<?php

declare(strict_types=1);

namespace PeServer\App\Controllers\Api;

use PeServer\Core\Mvc\ControllerArgument;
use PeServer\App\Controllers\DomainControllerBase;

abstract class ApiControllerBase extends DomainControllerBase
{
	protected function __construct(ControllerArgument $argument)
	{
		parent::__construct($argument);
	}

	#region DomainControllerBase

	protected function getSkipBaseName(): string
	{
		return __NAMESPACE__;
	}

	#endregion
}
