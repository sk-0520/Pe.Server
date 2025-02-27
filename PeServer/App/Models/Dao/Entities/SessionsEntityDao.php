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

	public function selectExistsBySessionId(string $sessionId): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				sessions
			where
				sessions.session_id = :session_id

			SQL,
			[
				"session_id" => $sessionId
			]
		);
	}

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

	public function updateSessionBySessionId(string $sessionId, string $data, DateTimeInterface $updatedTimestamp): bool
	{
		return $this->context->updateByKeyOrNothing(
			<<<SQL

			update
				sessions
			set
				updated_epoch = :updated_epoch,
				data = :data
			where
				session_id = :session_id

			SQL,
			[
				"session_id" => $sessionId,
				"updated_epoch" => $updatedTimestamp->getTimestamp(),
				"data" => $data,
			]
		);
	}

	public function deleteSessionBySessionId(string $sessionId): bool
	{
		return $this->context->deleteByKeyOrNothing(
			<<<SQL

			delete from
				sessions
			where
				sessions.session_id = :session_id

			SQL,
			[
				"session_id" => $sessionId,
			]
		);
	}

	public function deleteOldSessions(DateTimeInterface $safeTimestamp): int
	{
		return $this->context->delete(
			<<<SQL

			delete from
				sessions
			where
				sessions.updated_epoch < updated_epoch

			SQL,
			[
				"updated_epoch" => $safeTimestamp->getTimestamp(),
			]
		);
	}


	#endregion
}
