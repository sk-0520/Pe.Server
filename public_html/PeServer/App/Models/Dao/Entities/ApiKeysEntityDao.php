<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use DateTimeImmutable;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\IDatabaseContext;

class ApiKeysEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	public function selectExistsApiKeyByUserId(string $userId): bool
	{
		$result = $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				api_keys
			where
				api_keys.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);

		return $result === 1;
	}

	public function selectExistsApiKeyByApiKey(string $apiKey): bool
	{
		$result = $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				api_keys
			where
				api_keys.api_key = :api_key

			SQL,
			[
				'api_key' => $apiKey,
			]
		);

		return $result === 1;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return DatabaseRowResult
	 * @phpstan-return DatabaseRowResult<array{api_key:string,user_id:string,secret_key:string,created_timestamp:string}>
	 */
	public function selectApiKeyByUserId(string $userId): DatabaseRowResult
	{
		/** @phpstan-var DatabaseRowResult<array{api_key:string,user_id:string,secret_key:string,created_timestamp:string}> */
		return $this->context->querySingle(
			<<<SQL

			select
				api_keys.api_key,
				api_keys.user_id,
				api_keys.secret_key,
				api_keys.created_timestamp
			from
				api_keys
			where
				api_keys.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}

	public function insertApiKey(string $userId, string $apiKey, string $secret): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				api_keys
				(
					api_key,
					user_id,
					secret_key,
					created_timestamp
				)
				values
				(
					:api_key,
					:user_id,
					:secret_key,
					CURRENT_TIMESTAMP
				)

			SQL,
			[
				'user_id' => $userId,
				'api_key' => $apiKey,
				'secret_key' => $secret,
			]
		);
	}

	public function deleteApiKeyByUserId(string $userId): int
	{
		return $this->context->delete(
			<<<SQL

			delete from
				api_keys
			where
				api_keys.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}
}
