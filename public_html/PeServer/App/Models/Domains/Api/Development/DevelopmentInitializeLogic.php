<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use \PeServer\Core\LogicBase;
use \PeServer\Core\LogicParameter;

class DevelopmentInitializeLogic extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(int $logicMode): void
	{
		//NONE
	}

	protected function executeImpl(int $logicMode): void
	{
		//NONE
	}
}
