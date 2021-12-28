<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use \PeServer\Core\DaoBase;
use \PeServer\Core\Database;

class UserAuthenticationsEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	public function insertUserAuthentication(string $userId, string $generatePassword, string $currentPassword): void
	{
		$this->database->insertSingle(
			<<<SQL

			insert into
			user_authentications
				(
					user_id,
					generate_password,
					current_password
				)
				values
				(
					:user_id,
					:generate_password,
					:current_password
				)

			SQL,
			[
				'user_id' => $userId,
				'generate_password' => $generatePassword,
				'current_password' => $currentPassword
			]
		);
	}
}
