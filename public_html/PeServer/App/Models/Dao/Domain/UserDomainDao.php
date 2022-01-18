<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use PeServer\Core\Database\DaoBase;
use PeServer\App\Models\Cache\UserCache;
use PeServer\Core\Database\IDatabaseContext;

class UserDomainDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $loginId
	 * @return array{user_id:string,login_id:string,name:string,level:string,state:string,generate_password:string,current_password:string}|null
	 */
	public function selectLoginUser(string $loginId): ?array
	{
		/** @var array{user_id:string,login_id:string,name:string,level:string,state:string,generate_password:string,current_password:string}|null */
		return $this->context->querySingleOr(
			null,
			<<<SQL

			select
				users.user_id,
				users.login_id,
				users.name,
				users.level,
				users.state,
				user_authentications.generate_password,
				user_authentications.current_password
			from
				users
				inner join
					user_authentications
					on
					(
						user_authentications.user_id = users.user_id
					)
			where
				users.login_id = :login_id
				and
				(
					users.state = 'enabled'
				)

			SQL,
			[
				'login_id' => $loginId,
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return array{email:string,wait_email:string,token_timestamp_utc:string}
	 */
	public function selectEmailAndWaitTokenTimestamp(string $userId, int $limitMinutes): array
	{
		/** @var array{email:string,wait_email:string,token_timestamp_utc:string} */
		return $this->context->querySingle(
			<<<SQL

			select
				users.email,
				IFNULL(user_change_wait_emails.email, '') as wait_email,
				IFNULL(STRFTIME('%Y-%m-%dT%H:%M:%SZ', user_change_wait_emails.timestamp), '') as token_timestamp_utc
			from
				users
				left join
					user_change_wait_emails
					on
					(
						user_change_wait_emails.user_id = users.user_id
						and
						(STRFTIME('%s', CURRENT_TIMESTAMP) - STRFTIME('%s', user_change_wait_emails.timestamp)) < :limit_minutes * 60
					)
			where
				users.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'limit_minutes' => $limitMinutes,
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @return UserCache[]
	 */
	public function selectCacheItems(): array
	{
		$result = $this->context->query(
			<<<SQL

			select
				users.user_id,
				users.name,
				users.level,
				users.state,
				users.website,
				users.description
			from
				users
			order by
				users.user_id

			SQL
		);

		return array_map(function ($i) {
			$cache = new UserCache();

			$cache->userId = $i['user_id'];
			$cache->userName = $i['name'];
			$cache->level = $i['level'];
			$cache->state = $i['state'];
			$cache->website = $i['website'];
			$cache->description = $i['description'];

			return $cache;
		}, $result);
	}

	public function updateEmailFromWaitEmail(string $userId, string $token): bool
	{
		return $this->context->updateByKeyOrNothing(
			<<<SQL

			update
				users
			set
				email = (
					select
						user_change_wait_emails.email
					from
						user_change_wait_emails
					where
						user_change_wait_emails.user_id = users.user_id
						and
						user_change_wait_emails.token = :token
				),
				mark_email = (
					select
						user_change_wait_emails.mark_email
					from
						user_change_wait_emails
					where
						user_change_wait_emails.user_id = users.user_id
						and
						user_change_wait_emails.token = :token
				)
			where
				users.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
				'token' => $token,
			]
		);
	}
}
