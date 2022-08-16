<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * 単一問い合わせ結果。
 *
 * @template TFieldArray of FieldArrayAlias
 * @immutable
 */
class DatabaseRowResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param array<string|int,mixed> $fields フィールド一覧。
	 * @phpstan-param TFieldArray $fields
	 */
	public function __construct(
		array $columns,
		int $resultCount,
		public array $fields
	) {
		parent::__construct($columns, $resultCount);
	}
}
