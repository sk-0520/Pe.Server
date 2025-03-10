<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

/**
 * データベース情報。
 */
readonly class DatabaseSchemaItem
{
	/**
	 * 生成。
	 *
	 * @param DatabaseInformationItem $database データベース。
	 * @param string $name スキーマ名
	 * @param bool $isDefault 標準スキーマか。
	 */
	public function __construct(
		public DatabaseInformationItem $database,
		public string $name,
		public bool $isDefault
	) {
		//NOP
	}
}
