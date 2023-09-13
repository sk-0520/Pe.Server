<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use \DateTimeInterface;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\IDatabaseContext;

class UserAuthenticationsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * @template TFieldArray of array{current_password:string}
	 * @param string $userId
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function selectPassword(string $userId): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		return $this->context->querySingle(
			<<<SQL

			select
				user_authentications.current_password
			from
				user_authentications
			where
				user_authentications.user_id = :user_id

			SQL,
			[
				'user_id' => $userId
			]
		);
	}

	/**
	 *
	 * @template TFieldArray of array{user_id:string,reminder_token:string,reminder_timestamp:string}
	 * @param string $token
	 * @return DatabaseRowResult|null
	 * @phpstan-return DatabaseRowResult<TFieldArray>|null
	 */
	public function selectPasswordReminderByToken(string $token): ?DatabaseRowResult
	{
		/** @var DatabaseRowResult<TFieldArray>|null */
		return $this->context->querySingleOrNull(
			<<<SQL

			select
				user_authentications.current_password
			from
				user_authentications
			where
				user_authentications.reminder_token = :token

			SQL,
			[
				'token' => $token
			]
		);
	}

	public function selectExistsToken(string $token, int $limitMinutes): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				user_authentications
			where
				user_authentications.reminder_token = :token
				and
				(STRFTIME('%s', CURRENT_TIMESTAMP) - STRFTIME('%s', user_authentications.reminder_timestamp)) < :limit_minutes * 60

			SQL,
			[
				'token' => $token,
				'limit_minutes' => $limitMinutes,
			]
		);
	}

	public function insertUserAuthentication(string $userId, string $currentPassword): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
			user_authentications
				(
					user_id,
					reminder_token,
					reminder_timestamp,
					current_password
				)
				values
				(
					:user_id,
					'',
					NULL,
					:current_password
				)

			SQL,
			[
				'user_id' => $userId,
				'current_password' => $currentPassword
			]
		);
	}

	public function updateCurrentPassword(string $userId, string $currentPassword): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				user_authentications
			set
				current_password = :current_password
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'current_password' => $currentPassword
			]
		);
	}

	public function updatePasswordReminder(string $userId, string $token): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				user_authentications
			set
				reminder_token = :token,
				reminder_timestamp = CURRENT_TIMESTAMP
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'token' => $token,
			]
		);
	}

	public function updateResetPassword(string $userId, string $currentPassword): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				user_authentications
			set
				current_password = :current_password,
				reminder_token = '',
				reminder_timestamp = NULL
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'current_password' => $currentPassword,
			]
		);
	}

	public function updateClearReminder(string $userId): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				user_authentications
			set
				reminder_token = '',
				reminder_timestamp = NULL
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}
}
