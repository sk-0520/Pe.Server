<?php

declare(strict_types=1);

namespace PeServer\App\Models\Database\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\TypeConverter;

class UserAuditLogsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
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

	public function selectLastLogId(): int
	{
		$result = $this->context->queryFirst(
			<<<SQL

			select
				LAST_INSERT_ROWID() as [row_id]

			SQL
		);

		return intval($result['row_id']);
	}
}
