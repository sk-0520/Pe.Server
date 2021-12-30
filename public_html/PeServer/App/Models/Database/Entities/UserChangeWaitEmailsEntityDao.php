<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use \PeServer\Core\DaoBase;
use \PeServer\Core\Database;

class UserChangeWaitEmailsEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}
}
