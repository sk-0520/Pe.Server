<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\App\Models\Data\Dto\CrashReportListItemDto;
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
	public function selectExistsCrashReportsBySequence(int $sequence): bool
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

				'report' => $report->raw

			]
		);
	}

	public function deleteCrashReportsBySequence(int $sequence): void
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
