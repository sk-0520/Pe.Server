<?php

declare(strict_types=1);

namespace PeServer\Core\Database\Management;

/**
 * DB実装管理処理。
 */
interface IDatabaseManagement
{
	#region function

	/**
	 * DB 内のデータベース一覧を取得。
	 *
	 * @return DatabaseInformationItem[]
	 */
	public function getDatabaseItems(): array;

	/**
	 * スキーマ一覧を取得。
	 *
	 * @param DatabaseInformationItem $databaseItem 対象データベース。
	 * @return DatabaseSchemaItem[]
	 */
	public function getSchemaItems(DatabaseInformationItem $databaseItem): array;

	/**
	 * スキーマのリソースを取得。
	 *
	 * @param DatabaseSchemaItem $schemaItem
	 * @param int $kinds
	 * @phpstan-param int-mask-of<DatabaseResourceItem::KIND_*> $kinds
	 * @return DatabaseResourceItem[]
	 */
	public function getResourceItems(DatabaseSchemaItem $schemaItem, int $kinds): array;

	#endregion
}
