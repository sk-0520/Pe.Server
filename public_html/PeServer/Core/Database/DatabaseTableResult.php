<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\DatabaseResultBase;

/**
 * 問い合わせ結果。
 *
 * @template TFieldArray of FieldArrayAlias
 * @immutable
 */
class DatabaseTableResult extends DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param array<array<string|int,mixed>> $rows レコード一覧。各レコードにフィールド配列が格納されている。
	 * @phpstan-param TFieldArray[] $rows
	 */
	public function __construct(
		array $columns,
		int $resultCount,
		public array $rows
	) {
		parent::__construct($columns, $resultCount);
	}

	/**
	 * レコード件数。
	 *
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function getRowsCount(): int
	{
		return Arr::getCount($this->rows);
	}
}
