<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Domain;

use Exception;
use PeServer\App\Models\Cache\PluginCache;
use PeServer\App\Models\Cache\PluginCacheCategory;
use PeServer\App\Models\Cache\PluginCacheItem;
use PeServer\App\Models\Data\Dto\CrashReportDetailDto;
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

				nullif(crash_report_comments.comment, '') as developer_comment
			from
				crash_reports
				left join
					crash_report_comments
					on
					(
						crash_report_comments.crash_report_sequence = crash_reports.sequence
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

	#endregion
}
