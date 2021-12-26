<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use \PeServer\Core\DaoBase;
use \PeServer\Core\Database;

class UsersEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	public function selectExistsSetupUser(): bool
	{
		return (bool)$this->database->selectSingleCount(
			<<<SQL

			select
				COUNT(*) as count
			from
				users
			where
				users.level = 'setup'
				and
				users.state = 'enabled'

SQL
		);
	}

	public function selectExistsLoginId(string $loginId): bool
	{
		return (bool)$this->database->selectSingleCount(
			<<<SQL

			select
				COUNT(*) as count
			from
				users
			where
				users.login_id = :login_id

SQL
			/* AUTO-FORMAT */,
			[
				'login_id' => $loginId
			]
		);
	}

}
