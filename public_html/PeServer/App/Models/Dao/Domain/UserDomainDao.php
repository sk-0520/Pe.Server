<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use \DateTime;
use PeServer\App\Models\Cache\UserCache;
use PeServer\App\Models\Cache\UserCacheItem;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\TypeUtility;
use PeServer\Core\Utc;

class UserDomainDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	/**
	 * @template TFieldArray of array{user_id:string,login_id:string,name:string,level:string,state:string,generated_password:string,current_password:string}
	 * @param string $loginId
	 * @phpstan-return DatabaseRowResult<TFieldArray>|null
	 */
	public function selectLoginUser(string $loginId): ?DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray>|null */
		return $this->context->querySingleOrNull(
			<<<SQL

			select
				users.user_id,
				users.login_id,
				users.name,
				users.level,
				users.state,
				user_authentications.generated_password,
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
	 * @template TFieldArray of array{email:string,wait_email:string,token_timestamp_utc:string}
	 *
	 * @param string $userId
	 * @phpstan-return DatabaseRowResult<TFieldArray>
	 */
	public function selectEmailAndWaitTokenTimestamp(string $userId, int $limitMinutes): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<TFieldArray> */
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
	 * @return UserCacheItem[]
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
				users.description,
				IFNULL(api_keys.api_key, '') as api_key,
				IFNULL(api_keys.secret_key, '') as secret_key,
				api_keys.created_timestamp as api_created_timestamp
			from
				users
				left join
					api_keys
					on
					(
						api_keys.user_id = users.user_id
					)
			order by
				users.user_id

			SQL
		);

		return array_map(function ($i) {
			$rawApiTimestamp = $i['api_created_timestamp'];
			/** @var DateTime|null */
			$apiTimestamp = null;
			Utc::tryParseDateTime($rawApiTimestamp, $apiTimestamp);

			$cache = new UserCacheItem(
				$i['user_id'],
				$i['name'],
				$i['level'],
				$i['state'],
				$i['website'],
				$i['description'],
				$i['api_key'],
				$i['secret_key'],
				$apiTimestamp
			);

			return $cache;
		}, $result->rows);
	}

	public function selectUserIdFromApiKey(string $apiKey, string $secretKey): ?string
	{
		// 特に何かとくっつける必要はないが将来的になんか項目が増えたら面倒なのでこのクラスに実装

		/** @phpstan-var DatabaseRowResult<non-empty-array<string,string>>|null */
		$result = $this->context->querySingleOrNull(
			<<<SQL

			select
				api_keys.user_id
			from
				api_keys
			where
				api_keys.api_key = :api_key
				and
				api_keys.secret_key = :secret_key

			SQL,
			[
				'api_key' => $apiKey,
				'secret_key' => $secretKey,
			]
		);

		if ($result === null) {
			return null;
		}

		return $result->fields['user_id'];
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
