<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\DisposerBase;
use PeServerUT\Core\Database\DB;

class KeepDatabaseConnection extends DisposerBase implements IDatabaseConnection
{
	#region variable

	private DatabaseConnection $connection;
	private DatabaseContext|null $context = null;

	#endregion

	#region IDatabaseConnection

	public function __construct()
	{
		$this->connection = DB::memoryConnection();
	}

	public function getConnectionSetting(): ConnectionSetting
	{
		return $this->connection->getConnectionSetting();
	}

	public function open(): DatabaseContext
	{
		$this->throwIfDisposed();

		return $this->context ??= $this->connection->open();
	}

	#endregion

	#region DisposerBase

	protected function disposeImpl(): void
	{
		$this->context->dispose();
		$this->context = null;

		parent::disposeImpl();
	}

	#endregion
}
