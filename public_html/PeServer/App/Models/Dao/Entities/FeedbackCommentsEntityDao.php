<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Serialization\Mapper;

class FeedbackCommentsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function


	public function selectExistsFeedbackCommentsBySequence(int $sequence): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				feedback_comments
			where
				feedback_comments.feedback_sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);
	}

	public function insertFeedbackComments(int $sequence, string $comment): void
	{
		$this->context->insertSingle(
			<<<SQL

			insert into
				feedback_comments
				(
					[feedback_sequence],
					[comment]
				)
				values
				(
					:feedback_sequence,
					:comment
				)

			SQL,
			[
				'feedback_sequence' => $sequence,
				'comment' => $comment,
			]
		);
	}

	public function updateFeedbackComments(int $sequence, string $comment): void
	{
		$this->context->updateByKey(
			<<<SQL

			update
				feedback_comments
			set
				[comment] = :comment
			where
				[feedback_sequence] = :feedback_sequence

			SQL,
			[
				'feedback_sequence' => $sequence,
				'comment' => $comment,
			]
		);
	}

	public function deleteFeedbackCommentsBySequence(int $sequence): bool
	{
		return $this->context->deleteByKeyOrNothing(
			<<<SQL

			delete
			from
				feedback_comments
			where
				feedback_sequence = :feedback_sequence

			SQL,
			[
				'feedback_sequence' => $sequence,
			]
		);
	}

	#endregion
}
