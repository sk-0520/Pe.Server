<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Tool;

use Exception;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class ToolTextLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase


	protected function validateImpl(LogicCallMode $callMode): void
	{ }

	protected function executeImpl(LogicCallMode $callMode): void
	{ }

	#endregion
}
