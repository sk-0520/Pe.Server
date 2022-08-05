<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use \PDO;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;

/**
 * カラム情報
 *
 * @see https://www.php.net/manual/pdostatement.getcolumnmeta.php
 */
class DatabaseColumn
{
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

	public static function create(array $meta): self
	{
		return new DatabaseColumn(
			ArrayUtility::getOr($meta, 'name', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'len', 0),
			ArrayUtility::getOr($meta, 'precision', 0),
			ArrayUtility::getOr($meta, 'table', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'native_type', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'driver:decl_type', InitialValue::EMPTY_STRING),
			ArrayUtility::getOr($meta, 'pdo_type', -1),
			ArrayUtility::getOr($meta, 'flags', [])
		);
	}
}
