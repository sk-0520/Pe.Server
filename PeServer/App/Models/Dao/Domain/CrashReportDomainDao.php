<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use Exception;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Data\Dto\CrashReportDetailDto;
use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\Core\Collections\Collection;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class CrashReportDomainDao extends DaoBase
{
	use DaoTrait;

	#region function

	public function selectCrashReportsDetail(int $sequence): CrashReportDetailDto
	{
		$result = $this->context->querySingle(
			<<<SQL

			select
				crash_reports.sequence,

				crash_reports.timestamp,
				crash_reports.ip_address,

				crash_reports.version,
				crash_reports.revision,
				crash_reports.build,
				crash_reports.user_id,

				crash_reports.exception,

				crash_reports.email,
				crash_reports.comment,

				crash_reports.report,

				COALESCE(crash_report_comments.comment, '') as developer_comment,
				COALESCE(crash_report_status.status, 'none') as developer_status
			from
				crash_reports
				left join
					crash_report_comments
					on
					(
						crash_report_comments.crash_report_sequence = crash_reports.sequence
					)
				left join
					crash_report_status
					on
					(
						crash_report_status.crash_report_sequence = crash_reports.sequence
					)

			where
				crash_reports.sequence = :sequence

			SQL,
			[
				'sequence' => $sequence,
			]
		);

		return $result->mapping(CrashReportDetailDto::class);
	}

	/**
	 * クラッシュレポート ページ 全件数取得。
	 *
	 * @return int
	 * @phpstan-return non-negative-int
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
	 * @phpstan-param non-negative-int $index
	 * @param int $count
	 * @phpstan-param non-negative-int $count
	 * @return CrashReportListItemDto[]
	 */
	public function selectCrashReportsPageItems(int $index, int $count): array
	{
		$result = $this->context->selectOrdered(
			<<<SQL

			select
				crash_reports.sequence,
				crash_reports.timestamp,
				COALESCE(crash_report_status.status, 'none') as developer_status,
				crash_reports.version,
				REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10)) as exception_lf,
				case INSTR(REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10)), CHAR(10))
					when 0 then
						REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10))
					else
						SUBSTR(REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10)), 0, INSTR(REPLACE(REPLACE(crash_reports.exception, CHAR(13, 10), CHAR(10)), CHAR(13), CHAR(10)), CHAR(10)))
				end as exception_subject
			from
				crash_reports
				left join
					crash_report_status
					on
					(
						crash_report_status.crash_report_sequence = crash_reports.sequence
					)
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

		return $result->mapping(CrashReportListItemDto::class);
	}

	#endregion
}
