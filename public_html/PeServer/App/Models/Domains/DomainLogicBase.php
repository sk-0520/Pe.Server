<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;

abstract class DomainLogicBase extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}
}
