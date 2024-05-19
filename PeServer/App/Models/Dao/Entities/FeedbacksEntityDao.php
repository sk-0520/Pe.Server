<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\FeedbackDetailDto;
use PeServer\App\Models\Data\Dto\FeedbackListItemDto;
use PeServer\App\Models\Data\FeedbackDetail;
use PeServer\App\Models\Data\FeedbackListItem;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Collections;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\DatabaseRowResult;
use PeServer\Core\Database\DatabaseTableResult;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Serialization\Mapper;

class FeedbacksEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

	/**
	 * フィードバックを主キー検索で有無確認。
	 *
	 * @param int $sequence
	 * @return bool
	 */
	public function selectExistsFeedbacksBySequence(int $sequence): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				feedbacks
			where
				sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);
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

	public function insertFeedbacks(
		string $ipAddress,
		string $version,
		string $revision,
		string $build,
		string $userId,
		string $firstExecuteTimestamp,
		string $firstExecuteVersion,
		string $process,
		string $platform,
		string $os,
		string $clr,
		string $kind,
		string $subject,
		string $content
	): void {
		$this->context->insertSingle(
			<<<SQL

			insert into
				feedbacks
				(
					[timestamp],
					[ip_address],

					[version],
					[revision],
					[build],
					[user_id],

					[first_execute_timestamp],
					[first_execute_version],

					[process],
					[platform],
					[os],
					[clr],

					[kind],
					[subject],
					[content]
				)
				values
				(
					CURRENT_TIMESTAMP,
					:ip_address,

					:version,
					:revision,
					:build,
					:user_id,

					:first_execute_timestamp,
					:first_execute_version,

					:process,
					:platform,
					:os,
					:clr,

					:kind,
					:subject,
					:content
				)

			SQL,
			[
				'ip_address' => $ipAddress,

				'version' => $version,
				'revision' => $revision,
				'build' => $build,
				'user_id' => $userId,

				'first_execute_timestamp' => $firstExecuteTimestamp,
				'first_execute_version' => $firstExecuteVersion,

				'process' => $process,
				'platform' => $platform,
				'os' => $os,
				'clr' => $clr,

				'kind' => $kind,
				'subject' => $subject,
				'content' => $content,
			]
		);
	}

	public function deleteFeedbacksBySequence(int $sequence): void
	{
		$this->context->deleteByKey(
			<<<SQL

			delete
			from
				feedbacks
			where
				sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);
	}

	#endregion
}
