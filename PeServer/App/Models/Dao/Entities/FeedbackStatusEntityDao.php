<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Serialization\Mapper;

class FeedbackStatusEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function upsertFeedbackStatus(int $sequence, ReportStatus $status): void
	{
		$this->context->execute(
			<<<SQL

			insert into
				feedback_status
				(
					feedback_sequence,
					status
				)
				values
				(
					:feedback_sequence,
					:status
				)
				on
					conflict(feedback_sequence)
				do update
					set
						status = :status

			SQL,
			[
				"feedback_sequence" => $sequence,
				"status" => $status->value
			]
		);
	}

	public function deleteFeedbackStatusBySequence(int $sequence): bool
	{
		return $this->context->deleteByKeyOrNothing(
			<<<SQL

			delete
			from
				feedback_status
			where
				feedback_sequence = :feedback_sequence

			SQL,
			[
				"feedback_sequence" => $sequence,
			]
		);
	}


	#endregion
}
