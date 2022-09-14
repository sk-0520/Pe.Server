<?php

declare(strict_types=1);

namespace PeServer\App\Models\Dao\Entities;

use PeServer\Core\Binary;
use PeServer\Core\Database\DaoBase;
use PeServer\Core\Database\DaoTrait;
use PeServer\Core\Database\IDatabaseContext;

class FeedbacksEntityDao extends DaoBase
{
	use DaoTrait;

	#region function

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

	#endregion
}
