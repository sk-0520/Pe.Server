<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * 単一問い合わせ結果。
 */
class DatabaseRowResult extends DatabaseResultBase
{
	public function __construct(
		array $columns,
		int $resultCount,
		public array $fields
	) {
		parent::__construct($columns, $resultCount);
	}
}
