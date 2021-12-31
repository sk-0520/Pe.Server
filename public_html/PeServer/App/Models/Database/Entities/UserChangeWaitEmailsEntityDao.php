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

	public function insertWaitEmails(string $userId, string $email, string $token):void
	{
		$this->database->insertSingle(
			<<<SQL

			insert into
				user_change_wait_emails
				(
					user_id,
					token,
					timestamp,
					email
				)
				values
				(
					:user_id,
					:token,
					CURRENT_TIMESTAMP,
					:email
				)

			SQL,
			[
				'user_id' => $userId,
				'token' => $token,
				'email' => $email,
			]
		);
	}

	public function deleteByUserId(string $userId): bool
	{
		return $this->database->deleteByKeyOrNothing(
			<<<SQL

			delete
			from
				user_change_wait_emails
			where
				user_change_wait_emails.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}
}
