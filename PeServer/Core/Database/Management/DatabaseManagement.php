<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseContext;

/**
 * DB実装管理処理。
 *
 * NOTE: SQLite 前提。
 */
class DatabaseManagement implements IDatabaseManagement
{
	public function __construct(
		protected readonly IDatabaseContext $context
	) {
		//NOP
	}

	#region IDatabaseManagement

	public function getDatabaseItems(): array
	{
		$rows = $this->context->query(
			<<<SQL

			select
				pragma_database_list.name as name
			from
				pragma_database_list
			order by
				pragma_database_list.name asc

			SQL
		);

		return Arr::map(
			$rows->rows,
			fn($row) =>  new DatabaseInformation($row['name'])
		);
	}

	#endregion
}
