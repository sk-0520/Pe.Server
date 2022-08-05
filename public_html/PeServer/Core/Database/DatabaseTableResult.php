<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\ArrayUtility;

/**
 * 問い合わせ結果。
 */
class DatabaseTableResult extends DatabaseResultBase
{
	public function __construct(
		array $columns,
		int $resultCount,
		public array $rows
	) {
		parent::__construct($columns, $resultCount);
	}

	public function getRowsCount(): int
	{
		return ArrayUtility::getCount($this->rows);
	}
}
