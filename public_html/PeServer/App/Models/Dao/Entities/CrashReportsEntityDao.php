<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportListItem;
use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Serialization\Mapper;

class CrashReportsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function


	/**
	 * Undocumented function
	 *
	 * @param int $sequence
	 * @return bool
	 */
	public function selectExistsCrashReports(int $sequence): bool
	{
		return 1 === $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				crash_reports
			where
				crash_reports.sequence = :sequence

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
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function selectCrashReportsPageTotalCount(): int
	{
		return $this->context->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				crash_reports

			SQL
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param int $index
	 * @phpstan-param UnsignedIntegerAlias $index
	 * @param int $count
	 * @phpstan-param UnsignedIntegerAlias $count
	 * @return CrashReportListItem[]
	 */
	public function selectCrashReportsPageItems(int $index, int $count): array
	{
		$tableResult = $this->context->selectOrdered(
			<<<SQL

			select
				crash_reports.sequence,
				crash_reports.timestamp,
				crash_reports.version
			from
				crash_reports
			order by
				crash_reports.timestamp desc,
				crash_reports.sequence desc
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

		$result = [];
		$mapper = new Mapper();
		foreach ($tableResult->rows as $row) {
			$obj = new CrashReportListItem();
			$mapper->mapping($row, $obj);
			$result[] = $obj;
		}

		return $result;
	}

	public function insertCrashReports(
		string $ipAddress,
		string $version,
		string $revision,
		string $build,
		string $userId,
		string $exception,
		string $email,
		string $comment,
		Binary $report
	): void {
		$this->context->insertSingle(
			<<<SQL

			insert into
				crash_reports
				(
					[timestamp],
					[ip_address],

					[version],
					[revision],
					[build],
					[user_id],

					[exception],

					[email],
					[comment],

					[report]
				)
				values
				(
					CURRENT_TIMESTAMP,
					:ip_address,

					:version,
					:revision,
					:build,
					:user_id,

					:exception,

					:email,
					:comment,

					:report
				)

			SQL,
			[
				'ip_address' => $ipAddress,

				'version' => $version,
				'revision' => $revision,
				'build' => $build,
				'user_id' => $userId,

				'exception' => $exception,

				'email' => $email,
				'comment' => $comment,

				'report' => $report->getRaw()

			]
		);
	}

	#endregion
}
