<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;

/**
 * カラム情報。
 *
 * @immutable
 * @see https://www.php.net/manual/pdostatement.getcolumnmeta.php
 */
class DatabaseColumn
{
	/**
	 * 生成。
	 *
	 * @param string $name カラム名(name)。
	 * @param int $length カラム長(len)。
	 * @param int $precision 数値精度(precision)。
	 * @param string $table テーブル名(table)。
	 * @param string $nativeType PHP型(native_type)。
	 * @param string $driverType SQL型(driver:decl_type)。
	 * @param int $pdoType PDO型(pdo_type)。
	 * @phpstan-param -1|PDO::PARAM_* $pdoType
	 * @param array<mixed> $flags (flags)。
	 */
	public function __construct(
		public string $name,
		public int $length,
		public int $precision,
		public string $table,
		public string $nativeType,
		public string $driverType,
		public int $pdoType,
		public array $flags
	) {
	}

	#region function

	/**
	 * `PDOStatement::getColumnMeta` で取得した配列から `DatabaseColumn` の生成
	 *
	 * @param array<string,mixed> $meta
	 * @return self
	 */
	public static function create(array $meta): self
	{
		return new DatabaseColumn(
			Arr::getOr($meta, 'name', Text::EMPTY),
			Arr::getOr($meta, 'len', -1),
			Arr::getOr($meta, 'precision', 0),
			Arr::getOr($meta, 'table', Text::EMPTY),
			Arr::getOr($meta, 'native_type', Text::EMPTY),
			Arr::getOr($meta, 'driver:decl_type', Text::EMPTY),
			Arr::getOr($meta, 'pdo_type', -1),
			Arr::getOr($meta, 'flags', [])
		);
	}

	#endregion
}
