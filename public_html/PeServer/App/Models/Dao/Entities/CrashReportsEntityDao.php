<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\IDatabaseContext;

class CrashReportsEntityDao extends DaoBase
{
	public function __construct(IDatabaseContext $context)
	{
		parent::__construct($context);
	}

	#region function

	public function insertCrashReports(
		string $userId,
		string $version,
		string $revision,
		string $exception,
		string $email,
		string $comment,
		string $report
	): void {
		$this->context->insertSingle(
			<<<SQL

			insert into
				crash_reports
				(
					[timestamp],

					[user_id],
					[version],
					[revision],
					[exception],

					[email],
					[comment],

					[report]
				)
				values
				(
					CURRENT_TIMESTAMP,

					:user_id,
					:version,
					:revision,
					:exception,

					:email,
					:comment,

					:report
				)

			SQL,
			[
				'user_id' => $userId,
				'version' => $version,
				'revision' => $revision,
				'exception' => $exception,

				'email' => $email,
				'comment' => $comment,

				'report' => $report

			]
		);
	}

	#endregion
}
