<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * 問い合わせ結果格納データ基底。
 *
 * @immutable
 */
abstract class DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param DatabaseColumn[] $columns
	 * @param int $resultCount
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 */
	public function __construct(
		public array $columns,
		public int $resultCount
	) {
	}
}
