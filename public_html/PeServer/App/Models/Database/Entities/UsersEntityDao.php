<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class UsersEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	public function selectExistsSetupUser(): bool
	{
		return (bool)$this->context->selectSingleCount(
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
		return (bool)$this->context->selectSingleCount(
			<<<SQL

			select
				COUNT(*) as count
			from
				users
			where
				users.login_id = :login_id

			SQL,
			[
				'login_id' => $loginId
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return array{user_id:string,login_id:string,level:string,name:string,email:string,website:string}
	 */
	public function selectUserInfoData(string $userId): array
	{
		/** @var array{user_id:string,login_id:string,level:string,name:string,email:string,website:string} */
		return $this->context->querySingle(
			<<<SQL

			select
				users.user_id,
				users.login_id,
				users.level,
				users.name,
				users.email,
				users.website
			from
				users
			where
				users.user_id = :user_id

			SQL,
			[
				'user_id' => $userId
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return array{name:string,website:string}
	 */
	public function selectUserEditData(string $userId): array
	{
		/** @var array{name:string,website:string} */
		return $this->context->querySingle(
			<<<SQL

			select
				users.name,
				users.website
			from
				users
			where
				users.user_id = :user_id

			SQL,
			[
				'user_id' => $userId
			]
		);
	}

	public function selectEmail(string $userId): string
	{
		return $this->context->querySingle(
			<<<SQL

			select
				users.email
			from
				users
			where
				users.user_id = :user_id

			SQL,
			[
				'user_id' => $userId
			]
		)['email'];
	}

	public function insertUser(string $userId, string $loginId, string $level, string $state, string $userName, string $email, int $mark_email, string $website, string $note): void
	{
		$this->context->insertSingle(
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
					mark_email,
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
					:mark_email,
					:website,
					:note
				)

			SQL,
			[
				'user_id' => $userId,
				'login_id' => $loginId,
				'level' => $level,
				'state' => $state,
				'name' => $userName,
				'email' => $email,
				'mark_email' => $mark_email,
				'website' => $website,
				'note' => $note,
			]
		);
	}

	public function updateUserState(string $userId, string $state): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				users
			set
				state = :state
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'state' => $state,
			]
		);
	}

	public function updateUserSetting(string $userId, string $userName, string $website): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				users
			set
				name = :name,
				website = :website
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'name' => $userName,
				'website' => $website,
			]
		);
	}
}
