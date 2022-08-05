<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\ArrayUtility;

/**
 * 問い合わせ結果。
 *
 * @immutable
 */
class DatabaseTableResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param DatabaseColumn[] $columns
	 * @param int $resultCount
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 * @param array<array<string|int,mixed>> $rows
	 * @phpstan-param array<array<array-key,mixed>> $rows
	 */
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
