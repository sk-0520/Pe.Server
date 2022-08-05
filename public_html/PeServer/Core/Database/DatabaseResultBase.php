<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

/**
 * 問い合わせ結果格納データ基底。
 */
abstract class DatabaseResultBase
{
	/**
	 * 生成。
	 *
	 * @param DatabaseColumn[] $columns
	 * @param $resultCount
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 */
	public function __construct(
		public array $columns,
		public int $resultCount
	) {
	}
}
