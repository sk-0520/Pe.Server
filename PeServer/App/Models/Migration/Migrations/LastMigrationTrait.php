<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration\Migrations;

use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Throws\InvalidOperationException;

trait LastMigrationTrait
{
	#region function

	private function updateLastDatabase(int $version, IDatabaseContext $context): void
	{
		$this->logger->info("UPDATE");
		$result = $context->execute(
			<<<SQL

			replace into
				database_version
				(
					version
				)
				values
				(
					:version
				)

			SQL,
			[
				'version' => $version,
			]
		);

		if ($result->getResultCount() !== 1) {
			throw new InvalidOperationException("replace {$version}");
		}
	}

	#endregion
}
