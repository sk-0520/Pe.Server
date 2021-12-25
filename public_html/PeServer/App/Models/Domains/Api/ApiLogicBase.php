<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api;

use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\App\Models\Domains\DomainLogicBase;

abstract class ApiLogicBase extends DomainLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}
}
