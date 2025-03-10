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
	 * @return DatabaseInformation[]
	 */
	public function getDatabaseItems(): array;

	#endregion
}
