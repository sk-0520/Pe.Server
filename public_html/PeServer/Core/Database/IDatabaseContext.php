<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\IDatabaseExecutor;

interface IDatabaseContext extends IDatabaseReader, IDatabaseExecutor
{
	//NONE
}
