<?php

declare(strict_types=1);

namespace PeServerUT\Core\Migration;

use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationBase;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;
use PeServer\Core\Migration\SqliteMigrationRunnerBase;
use PeServerTest\TestClass;

class SqliteMigrationRunnerBaseTest extends TestClass
{
	#region function

	public function test_void()
	{
		// やぁだるい
		// インメモリでファイル判定とか今考える問題じゃないわ
		$this->success();
	}

	#endregion
}

class SqliteMigrationRunnerBaseTestClass extends SqliteMigrationRunnerBase
{
	public function __construct(private int $version)
	{
		parent::__construct(LoggerFactory::createNullFactory());
	}

	protected function getCurrentVersionCore(string $mode, IDatabaseContext $context): int
	{
		$checkCount = $context->selectSingleCount("select COUNT(*) from sqlite_master where sqlite_master.type='table' and sqlite_master.name='Version'");
		if (0 < $checkCount) {
			$row = $context->queryFirstOrNull("select v from Version");
			if ($row !== null) {
				return (int)$row->fields['v'];;
			}
		}

		return MigrationVersion::INITIAL_VERSION;
	}

	public function execute(): void
	{
		assert(false);
	}
}

#[MigrationVersion(1)]
class LocalSqliteMigrationClass1 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("create table Version(v integer not null)");
		$argument->context->execute("insert into Version(v) values(1)");
	}
}

#[MigrationVersion(2)]
class LocalSqliteMigrationClass2 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("insert into Version(v) values(2)");
	}
}
