<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Migration\AppMigrationRunner;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseConnection;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DisposerBase;
use PeServer\Core\Log\LoggerFactory;
use PeServerUT\Core\Database\DB;

/**
 * ユニットテストにおけるアプリケーション層DB構築処理を担当。
 */
class UtAppDatabase extends DisposerBase
{
	/** 通常接続先 */
	public LocalDatabaseConnection $default;
	/** セッション接続先 */
	public LocalDatabaseConnection $session;

	public function __construct()
	{
		$this->default = new LocalDatabaseConnection();
		$this->session = new LocalDatabaseConnection();

		$migrationRunner = new AppMigrationRunner(
			$this->default,
			$this->session,
			LoggerFactory::createNullFactory(),
		);
		$migrationRunner->execute();
	}

	#region DisposerBase

	protected function disposeImpl(): void
	{
		$this->default->dispose();
		$this->session->dispose();
	}

	#endregion
}

//phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
class LocalDatabaseConnection extends DisposerBase implements IDatabaseConnection
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
