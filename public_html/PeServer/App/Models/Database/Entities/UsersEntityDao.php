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

	public function insertUser(string $userId, string $loginId, string $level, string $state, string $userName, string $email, string $website, string $note): void
	{
		$this->database->insertSingle(
			<<<SQL

			insert into
				users
				(
					user_id,
					login_id,
					level,
					state,
					name,
					email,
					website,
					note
				)
				values
				(
					:user_id,
					:login_id,
					:level,
					:state,
					:name,
					:email,
					:website,
					:note
				)

			SQL
			/* AUTO-FORMAT */,
			[
				'user_id' => $userId,
				'login_id' => $loginId,
				'level' => $level,
				'state' => $state,
				'name' => $userName,
				'email' => $email,
				'website' => $website,
				'note' => $note,
			]
		);
	}

	public function updateUserState(string $userId, string $state): void
	{
		$this->database->updateByKey(
			<<<SQL

			update
				users
			set
				state = :state
			where
				user_id = :user_id

			SQL
			/* AUTO-FORMAT */,
			[
				'user_id' => $userId,
				'state' => $state,
			]
		);
	}
}
