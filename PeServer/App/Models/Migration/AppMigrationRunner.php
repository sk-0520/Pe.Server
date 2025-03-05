<?php

declare(strict_types=1);

namespace PeServer\App\Models\Migration;

use PeServer\App\Models\Migration\Migrations\Default\DefaultMigrationBase;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0000;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0001;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0002;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0003;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0004;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0005;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigration0006;
use PeServer\App\Models\Migration\Migrations\Default\DefaultMigrationLast;
use PeServer\App\Models\Migration\Migrations\Session\SessionMigrationBase;
use PeServer\App\Models\Migration\Migrations\Session\SessionMigration0000;
use PeServer\App\Models\Migration\Migrations\Session\SessionMigrationLast;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Migration\MigrationVersion;
use PeServer\Core\Migration\SqliteMigrationRunnerBase;

/**  */
class AppMigrationRunner extends SqliteMigrationRunnerBase
{
	#region variable

	/** @var class-string<DefaultMigrationBase>[] */
	private array $defaultMigrations = [
		DefaultMigration0000::class,
		DefaultMigration0001::class,
		DefaultMigration0002::class,
		DefaultMigration0003::class,
		DefaultMigration0004::class,
		DefaultMigration0005::class,
		DefaultMigration0006::class,
	];
	/** @var class-string<SessionMigrationBase>[] */
	private array $sessionMigrations = [
		SessionMigration0000::class
	];

	#endregion

	public function __construct(
		private IDatabaseConnection $defaultConnection,
		private IDatabaseConnection|null $sessionConnection,
		ILoggerFactory $loggerFactory
	) {
		parent::__construct($loggerFactory);
	}

	#region SqliteMigrationRunnerBase

	protected function getCurrentVersionCore(string $mode, IDatabaseContext $context): int
	{
		$checkCount = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='database_version'");
		if (0 < $checkCount) {
			$row = $context->queryFirstOrNull("select version from database_version");
			if ($row !== null) {
				return (int)$row->fields['version'];
				;
			}
		}

		return MigrationVersion::INITIAL_VERSION;
	}

	public function execute(): void
	{
		$this->executeCore("DB:DEFAULT", $this->defaultConnection, $this->defaultMigrations, DefaultMigrationLast::class);

		if ($this->sessionConnection !== null) {
			$this->executeCore("DB:SESSION", $this->sessionConnection, $this->sessionMigrations, SessionMigrationLast::class);
		}
	}

	#endregion
}
