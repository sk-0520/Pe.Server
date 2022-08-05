<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;

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
	 * @param $name カラム名(name)。
	 * @param $length カラム長(len)。
	 * @param $precision 数値精度(precision)。
	 * @param $table テーブル名(table)。
	 * @param $nativeType PHP型(native_type)。
	 * @param $driverType SQL型(driver:decl_type)。
	 * @param $pdoType PDO型(pdo_type)。
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

	/**
	 * `PDOStatement::getColumnMeta` で取得した配列から `DatabaseColumn` の生成
	 *
	 * @param array<string,mixed> $meta
	 * @return self
	 */
	public static function create(array $meta): self
	{
		return new DatabaseColumn(
			ArrayUtility::getOr($meta, 'name', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'len', -1),
			ArrayUtility::getOr($meta, 'precision', 0),
			ArrayUtility::getOr($meta, 'table', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'native_type', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'driver:decl_type', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'pdo_type', -1),
			ArrayUtility::getOr($meta, 'flags', [])
		);
	}
}
