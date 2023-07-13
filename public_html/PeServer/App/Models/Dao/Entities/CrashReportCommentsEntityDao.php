<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Serialization\Mapper;

class CrashReportCommentsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function


	public function selectExistsCrashReportCommentsBySequence(int $sequence): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				crash_report_comments
			where
				crash_report_comments.crash_report_sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);
	}

	public function insertCrashReportComments(int $sequence, string $comment): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				crash_report_comments
				(
					[crash_report_sequence],
					[comment]
				)
				values
				(
					:crash_report_sequence,
					:comment
				)

			SQL,
			[
				'crash_report_sequence' => $sequence,
				'comment' => $comment,
			]
		);
	}

	public function updateCrashReportComments(int $sequence, string $comment): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				crash_report_comments
			set
				[comment] = :comment
			where
				[crash_report_sequence] = :crash_report_sequence

			SQL,
			[
				'crash_report_sequence' => $sequence,
				'comment' => $comment,
			]
		);
	}

	public function deleteCrashReportCommentsBySequence(int $sequence): bool
	{
		return $this->context->deleteByKeyOrNothing(
			<<<SQL

			delete
			from
				crash_report_comments
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
