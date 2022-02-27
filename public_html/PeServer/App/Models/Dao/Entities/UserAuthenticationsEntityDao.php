<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class UserAuthenticationsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return array{generated_password:string,current_password:string}
	 */
	public function selectPasswords(string $userId): array
	{
		/** @var array{generated_password:string,current_password:string} */
		return $this->context->querySingle(
			<<<SQL

			select
				user_authentications.generated_password,
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

	public function insertUserAuthentication(string $userId, string $generatedPassword, string $currentPassword): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
			user_authentications
				(
					user_id,
					generated_password,
					current_password
				)
				values
				(
					:user_id,
					:generated_password,
					:current_password
				)

			SQL,
			[
				'user_id' => $userId,
				'generated_password' => $generatedPassword,
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
