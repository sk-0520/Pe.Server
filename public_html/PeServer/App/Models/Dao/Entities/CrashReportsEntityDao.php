<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportDetailDto;
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
	 * クラッシュレポートを主キー検索で有無確認。
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
	 * クラッシュレポート ページ 全件数取得。
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
	 * クラッシュレポート ページ 表示データ取得。
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

			with alias__crash_reports as (
				select
					crash_reports.*,
					REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10)) as exception_lf
				from
					crash_reports
			)
			select
				alias__crash_reports.sequence,
				alias__crash_reports.timestamp,
				alias__crash_reports.version,
				alias__crash_reports.exception_lf,
				case INSTR(alias__crash_reports.exception_lf, CHAR(10))
					when 0 then
						alias__crash_reports.exception_lf
					else
						SUBSTR(alias__crash_reports.exception_lf, 0, INSTR(alias__crash_reports.exception_lf, CHAR(10)))
				end as exception_subject
			from
				alias__crash_reports
			order by
				alias__crash_reports.timestamp desc,
				alias__crash_reports.sequence desc
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

	public function selectCrashReportsDetail(int $sequence): CrashReportDetailDto
	{
		$row = $this->context->querySingle(
			<<<SQL

			select
				crash_reports.*
			from
				crash_reports
			where
				sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);

		$mapper = new Mapper();
		$obj = new CrashReportDetailDto();
		$mapper->mapping($row->fields, $obj);

		return $obj;
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

	public function deleteCrashReport(int $sequence): void
	{
		$this->context->deleteByKey(
			<<<SQL

			delete
			from
				crash_reports
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
