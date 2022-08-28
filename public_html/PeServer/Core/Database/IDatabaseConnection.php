<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\DatabaseContext;

interface IDatabaseConnection
{
	#region function

	/**
	 * DB接続を開く。
	 *
	 * @return DatabaseContext
	 */
	function open(): DatabaseContext;

	#endregion
}
