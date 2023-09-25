<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\UserInformationDto;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DatabaseRowResult;
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
	 * @param string $loginId
	 * @return string|null
	 */
	public function selectUserIdByLoginId(string $loginId): string|null
	{
		/** @var DatabaseRowResult<array{user_id:string}>|null */
		$row = $this->context->queryFirstOrNull(
			<<<SQL

			select
				users.user_id
			from
				users
			where
				users.login_id = :login_id

			SQL,
			[
				'login_id' => $loginId
			]
		);

		if ($row === null) {
			return $row;
		}
		return $row->fields['user_id'];
	}

	/**
	 * @param string $userId
	 * @return UserInformationDto
	 */
	public function selectUserInfoData(string $userId): UserInformationDto
	{
		$result = $this->context->querySingle(
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

		return $result->mapping(UserInformationDto::class);
	}

	/**
	 * @template TFieldArray of array{name:string,website:string}
	 * @param string $userId
	 * @return DatabaseRowResult
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function selectUserEditData(string $userId): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray> */
		return $this->context->querySingle(
			<<<SQL

			select
				users.name,
				users.website,
				users.description
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
	 * @param string $userId
	 * @return string
	 */
	public function selectEmail(string $userId): string
	{
		/** @phpstan-var DatabaseRowResult<array{email:string}> */
		$result = $this->context->querySingle(
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
		);

		return $result->fields['email'];
	}

	public function insertUser(string $userId, string $loginId, string $level, string $state, string $userName, string $email, int $markEmail, string $website, string $description, string $note): void
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
					description,
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
					:description,
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
				'mark_email' => $markEmail,
				'website' => $website,
				'description' => $description,
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

	public function updateUserSetting(string $userId, string $userName, string $website, string $description): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				users
			set
				name = :name,
				website = :website,
				description = :description
			where
				user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'name' => $userName,
				'website' => $website,
				'description' => $description,
			]
		);
	}
}
