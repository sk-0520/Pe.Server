<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Domains;

use \PeServer\Core\DaoBase;
use \PeServer\Core\Database;

class UserDomainDao extends DaoBase
{
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $loginId
	 * @return array{user_id:string,login_id:string,name:string,level:string,state:string,generate_password:string,current_password:string}|null
	 */
	public function selectLoginUser(string $loginId)
	{
		return $this->database->queryFirstOrDefault(
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
		return $this->database->queryFirst(
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
}
