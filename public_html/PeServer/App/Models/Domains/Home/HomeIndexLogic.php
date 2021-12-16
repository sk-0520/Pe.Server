<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Home;

use \PeServer\Core\Mvc\LogicMode;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;

class HomeIndexLogic extends LogicBase
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
