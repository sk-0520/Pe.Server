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
	public KeepDatabaseConnection $default;
	/** セッション接続先 */
	public KeepDatabaseConnection $session;

	public function __construct()
	{
		$this->default = new KeepDatabaseConnection();
		$this->session = new KeepDatabaseConnection();

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
