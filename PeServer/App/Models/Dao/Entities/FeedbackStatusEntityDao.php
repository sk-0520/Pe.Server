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
					:sequence,
					:status
				)
				on
					conflict(feedback_sequence)
				do update
					set
						status = :status

			SQL,

			[
				"sequence" => $sequence,
				"status" => $status->value
			]
		);
	}


	#endregion
}
