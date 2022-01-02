<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use PeServer\Core\DaoBase;
use PeServer\Core\Database;

class UserChangeWaitEmailsEntityDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	public function selectExistsToken(string $userId, string $token, int $limitMinutes): bool
	{
		return 0 < $this->database->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				user_change_wait_emails
			where
				user_change_wait_emails.user_id = :user_id
				and
				user_change_wait_emails.token = :token
				and
				(STRFTIME('%s', CURRENT_TIMESTAMP) - STRFTIME('%s', user_change_wait_emails.timestamp)) < :limit_minutes * 60

			SQL,
			[
				'user_id' => $userId,
				'token' => $token,
				'limit_minutes' => $limitMinutes,
			]
		);
	}

	public function insertWaitEmails(string $userId, string $email, int $markEmail, string $token): void
	{
		$this->database->insertSingle(
			<<<SQL

			insert into
				user_change_wait_emails
				(
					user_id,
					token,
					timestamp,
					email,
					mark_email
				)
				values
				(
					:user_id,
					:token,
					CURRENT_TIMESTAMP,
					:email,
					:mark_email
				)

			SQL,
			[
				'user_id' => $userId,
				'token' => $token,
				'email' => $email,
				'mark_email' => $markEmail,
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
