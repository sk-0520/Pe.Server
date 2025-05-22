<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use Exception;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Data\Dto\CrashReportDetailDto;
use PeServer\App\Models\Data\Dto\FeedbackDetailDto;
use PeServer\App\Models\Data\Dto\FeedbackListItemDto;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class FeedbackDomainDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectFeedbackDetailBySequence(int $sequence): FeedbackDetailDto
	{
		$result = $this->context->querySingle(
			<<<SQL

			select
				feedbacks.sequence,

				feedbacks.timestamp,
				feedbacks.ip_address,

				feedbacks.version,
				feedbacks.revision,
				feedbacks.build,

				feedbacks.user_id,

				feedbacks.first_execute_timestamp,
				feedbacks.first_execute_version,

				feedbacks.process,
				feedbacks.platform,
				feedbacks.os,
				feedbacks.clr,

				feedbacks.kind,
				feedbacks.subject,
				feedbacks.content,

				nullif(feedback_comments.comment, '') as developer_comment,
				nullif(feedback_status.status, 'none') as developer_status
			from
				feedbacks
				left join
					feedback_comments
					on
					(
						feedback_comments.feedback_sequence = feedbacks.sequence
					)
			left join
				feedback_status
				on
				(
					feedback_status.feedback_sequence = feedbacks.sequence
				)

			where
				sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);

		return $result->mapping(FeedbackDetailDto::class);
	}

		/**
         * フィードバック ページ 全件数取得。
         *
         * @return int
         * @phpstan-return non-negative-int
         */
	public function selectFeedbacksPageTotalCount(): int
	{
		return $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				feedbacks
				feedbacks

			SQL
		);
	}


	/**
	 * フィードバック ページ 表示データ取得。
	 *
	 * @param int $index
	 * @phpstan-param non-negative-int $index
	 * @param int $count
	 * @phpstan-param non-negative-int $count
	 * @return FeedbackListItemDto[]
	 */
	public function selectFeedbacksPageItems(int $index, int $count): array
	{
		$result = $this->context->selectOrdered(
			<<<SQL

			select
				feedbacks.sequence,
				feedbacks.timestamp,
				feedbacks.version,
				feedbacks.kind,
				feedbacks.subject
			from
				feedbacks
			order by
				feedbacks.timestamp desc,
				feedbacks.sequence desc
			limit
				:count
			offset
				:index

			SQL,
			[
				'index' => $index,
				'count' => $count,
			]
		);

		return $result->mapping(FeedbackListItemDto::class);
	}

	#endregion
}
