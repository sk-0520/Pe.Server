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
	 * @param DatabaseColumn[] $columns カラム情報(取得成功したものだけ格納されている)。
	 * @param int $resultCount 実行影響件数。
	 * @phpstan-param UnsignedIntegerAlias $resultCount
	 */
	public function __construct(
		/** @readonly */
		public array $columns,
		/** @readonly */
		private int $resultCount
	) {
	}

	#region function

	/**
	 * 実行影響件数を取得。
	 *
	 * @return int
	 * @phpstan-return UnsignedIntegerAlias
	 */
	public function getResultCount(): int
	{
		return $this->resultCount;
	}

	#endregion
}
