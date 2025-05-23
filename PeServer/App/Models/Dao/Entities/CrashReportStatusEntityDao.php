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

class CrashReportStatusEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function upsertCrashReportStatus(int $sequence, ReportStatus $status): void
	{
		$this->context->execute(
			<<<SQL

			insert into
				crash_report_status
				(
					crash_report_sequence,
					status
				)
				values
				(
					:crash_report_sequence,
					:status
				)
				on
					conflict(crash_report_sequence)
				do update
					set
						status = :status

			SQL,
			[
				"crash_report_sequence" => $sequence,
				"status" => $status->value
			]
		);
	}

	public function deleteCrashReportStatusBySequence(int $sequence): bool
	{
		return $this->context->deleteByKeyOrNothing(
			<<<SQL

			delete
			from
				crash_report_status
			where
				crash_report_sequence = :crash_report_sequence

			SQL,
			[
				'crash_report_sequence' => $sequence,
			]
		);
	}


	#endregion
}
