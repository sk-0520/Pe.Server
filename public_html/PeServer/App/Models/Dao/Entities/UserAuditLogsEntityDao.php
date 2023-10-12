<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;

class UserAuditLogsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectLastLogId(): int
	{
		$result = $this->context->queryFirst(
			<<<SQL

			select
				LAST_INSERT_ROWID() as [row_id]

			SQL
		);

		return intval($result->fields['row_id']);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $userId
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function selectAuditLogsPageTotalCountFromUserId(string $userId): int
	{
		return $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				user_audit_logs
			where
				user_audit_logs.user_id = :user_id

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @template TFieldArray of array{timestamp:string,event:string,info:string,ip_address:string,user_agent:string}
	 * @param string $userId
	 * @param int $index
	 * @param int $count
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function selectAuditLogsPageItemsFromUserId(string $userId, int $index, int $count): DatabaseTableResult
	{
		/** @var DatabaseTableResult<TFieldArray> */
		return $this->context->selectOrdered(
			<<<SQL

			select
				user_audit_logs.timestamp,
				user_audit_logs.event,
				user_audit_logs.info,
				user_audit_logs.ip_address,
				user_audit_logs.user_agent
			from
				user_audit_logs
			where
				user_audit_logs.user_id = :user_id
			order by
				user_audit_logs.sequence desc
			limit
				:count
			offset
				:index

			SQL,
			[
				'user_id' => $userId,
				'index' => $index,
				'count' => $count,
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @template TFieldArray of array{timestamp:string,event:string,info:string,ip_address:string,user_agent:string}
	 * @param string $userId
	 * @return DatabaseTableResult
	 * @phpstan-return DatabaseTableResult<TFieldArray>
	 */
	public function selectAuditLogsFromUserId(string $userId): DatabaseTableResult
	{
		/** @var DatabaseTableResult<TFieldArray> */
		return $this->context->selectOrdered(
			<<<SQL

			select
				user_audit_logs.timestamp,
				user_audit_logs.event,
				user_audit_logs.info,
				user_audit_logs.ip_address,
				user_audit_logs.user_agent
			from
				user_audit_logs
			where
				user_audit_logs.user_id = :user_id
			order by
				user_audit_logs.sequence

			SQL,
			[
				'user_id' => $userId,
			]
		);
	}

	public function insertLog(string $userId, string $event, string $info, string $ipAddress, string $userAgent): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				user_audit_logs
				(
					[user_id],
					[timestamp],
					[event],
					[info],
					[ip_address],
					[user_agent]
				)
				values
				(
					:user_id,
					CURRENT_TIMESTAMP,
					:event,
					:info,
					:ip_address,
					:user_agent
				)

			SQL,
			[
				'user_id' => $userId,
				'event' => $event,
				'info' => $info,
				'ip_address' => $ipAddress,
				'user_agent' => $userAgent,
			]
		);
	}

	#endregion
}
