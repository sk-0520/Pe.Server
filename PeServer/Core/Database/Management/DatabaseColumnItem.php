<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

/**
 * カラム情報。
 */
readonly class DatabaseColumnItem
{
	/**
	 * 生成
	 *
	 */
	public function __construct(
		public DatabaseResourceItem $tableResource,
		public string $name,
		public int $position,
		public bool $isPrimary,
		public bool $isNullable,
		public string $defaultValue,
		public DatabaseColumnType $type
	) {
		//NOP
	}
}
