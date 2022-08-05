<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * 単一問い合わせ結果。
 *
 * @immutable
 */
class DatabaseRowResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param DatabaseColumn[] $columns
	 * @param int $resultCount
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 * @param array<string|int,mixed> $fields
	 * @phpstan-param array<array-key,mixed> $fields
	 */
	public function __construct(
		array $columns,
		int $resultCount,
		public array $fields
	) {
		parent::__construct($columns, $resultCount);
	}
}
