<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

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
	 * @return array{generate_password:string,current_password:string}
	 */
	public function selectPasswords(string $userId): array
	{
		/** @var array{generate_password:string,current_password:string} */
		return $this->context->querySingle(
			<<<SQL

			select
				user_authentications.generate_password,
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

	public function insertUserAuthentication(string $userId, string $generatePassword, string $currentPassword): void
	{
		$this->context->insertSingle(
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
