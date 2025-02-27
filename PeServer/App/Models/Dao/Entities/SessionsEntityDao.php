<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use DateTimeInterface;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Time;
use PeServer\Core\Utc;

class SessionsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectSessionDataBySessionId(string $sessionId): string|null
	{
		$result = $this->context->queryFirstOrNull(
			<<<SQL

			select
				sessions.data
			from
				sessions
			where
				sessions.session_id = :session_id

			SQL,
			[
				"session_id" => $sessionId
			]
		);

		if ($result === null) {
			return null;
		}

		return $result->fields["data"];
	}

	public function upsertSessionDataBySessionId(string $sessionId, string $data, DateTimeInterface $updatedTimestamp): void
	{
		$this->context->execute(
			<<<SQL

			insert into
				sessions
				(
					session_id,
					created_epoch,
					updated_epoch,
					data
				)
				values
				(
					:session_id,
					:created_epoch,
					:updated_epoch,
					:data
				)
				on
					conflict(session_id)
				do update
					set
						created_epoch = :updated_epoch,
						data = :data

			SQL,
			[
				"session_id" => $sessionId,
				"created_epoch" => $updatedTimestamp->getTimestamp(),
				"updated_epoch" => $updatedTimestamp->getTimestamp(),
				"data" => $data,
			]
		);
	}

	#endregion
}
