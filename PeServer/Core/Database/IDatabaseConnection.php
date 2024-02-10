<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\DatabaseContext;

/**
 * DB接続。
 */
interface IDatabaseConnection
{
	#region function

	/**
	 * DB接続情報を取得。
	 *
	 * @return ConnectionSetting
	 */
	public function getConnectionSetting(): ConnectionSetting;

	/**
	 * DB接続を開く。
	 *
	 * @return DatabaseContext
	 */
	public function open(): DatabaseContext;

	#endregion
}
