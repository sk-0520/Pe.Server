<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Home;

use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;

class HomeContactLogic extends PageLogicBase
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
		/**
		 * @var array<string,string>
		 * @phpstan-var array<non-empty-string,string>
		 */
		$families = AppConfiguration::$config['config']['address']['families'];
		foreach ($families as $key => $value) {
			$this->setValue($key, $value);
		}
	}
}
