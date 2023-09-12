<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

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
}
