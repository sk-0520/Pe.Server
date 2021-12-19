<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Home;

use \PeServer\Core\Mvc\LogicCallMode;
use \PeServer\Core\Mvc\LogicBase;
use \PeServer\Core\Mvc\LogicParameter;

class HomeContactLogic extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		//NONE
	}
}
