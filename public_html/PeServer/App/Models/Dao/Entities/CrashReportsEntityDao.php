<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class CrashReportsEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

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
