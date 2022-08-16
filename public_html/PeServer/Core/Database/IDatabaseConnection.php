<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\Database;

interface IDatabaseConnection
{
	/**
	 * DB接続を開く。
	 *
	 * @return Database
	 */
	function open(): Database;
}
