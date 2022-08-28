<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\IDatabaseExecutor;
use PeServer\Core\Database\IDatabaseReader;
use PeServer\Core\IDisposable;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\TransactionException;

interface IDatabaseContext extends IDatabaseReader, IDatabaseExecutor
{
	#region function

	/**
	 * トランザクション中か。
	 *
	 * @return bool
	 */
	public function inTransaction(): bool;

	#endregion
}
