<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Mvc\Logic\LogicParameter;

abstract class ManagementDatabaseBase extends PageLogicBase
{
	protected function __construct(LogicParameter $parameter, protected AppConfiguration $appConfig)
	{
		parent::__construct($parameter);
	}

	protected function getTargetDatabase(): DatabaseContext
	{
		throw new \Error();
	}
}
