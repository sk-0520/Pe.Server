<?php

declare(strict_types=1);

namespace PeServerUT\Core\Migration;

use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationBase;
use PeServer\Core\Migration\MigrationRunnerBase;
use PeServer\Core\Migration\MigrationTrait;
use PeServer\Core\Migration\MigrationVersion;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PeServerUT\Core\Database\DB;
use PHPUnit\Framework\Attributes\DataProvider;

class MigrationRunnerBaseTest extends TestClass
{
	#region function

	public static function provider_executeCore_throw_sorted()
	{
		return [
			[
				"length: 2 !== 1(sort/unique)",
				[
					LocalMigrationClass1::class,
					LocalMigrationClass1::class,
				]
			],
			[
				"index: 0",
				[
					LocalMigrationClass2::class,
					LocalMigrationClass1::class,
				]
			],
			[
				"index: 1",
				[
					LocalMigrationClass1::class,
					LocalMigrationClass3::class,
					LocalMigrationClass2::class,
				]
			],
		];
	}

	#[DataProvider('provider_executeCore_throw_sorted')]
	public function test_executeCore_throw_sorted(string $expected, array $migrationClasses)
	{
		$obj = new LocalMigrationRunnerBaseTestClass(0);
		$connection = DB::memoryConnection();

		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($expected);

		$this->callInstanceMethod($obj, "executeCore", ["", $connection, $migrationClasses, LocalMigrationClassLast::class]);
	}

	public function test_executeCore_normal()
	{
		$migrationClasses = [
			LocalMigrationClass1::class,
			LocalMigrationClass2::class,
			LocalMigrationClass3::class,
			LocalMigrationClass4::class,
		];
		$obj = new LocalMigrationRunnerBaseTestClass(MigrationVersion::INITIAL_VERSION);

		$connection = DB::memoryKeepConnection();

		$versionSql = <<<SQL
			select
				count(*)
			from
				sqlite_master
			where
				sqlite_master.name = 'Version'
		SQL;

		$context = $connection->open();
		$actual1 = $context->selectSingleCount($versionSql);
		$this->assertSame(0, $actual1);

		$this->callInstanceMethod($obj, "executeCore", ["", $connection, $migrationClasses, LocalMigrationClassLast::class]);

		$actual2 = $context->selectSingleCount($versionSql);
		$this->assertSame(1, $actual2);
	}

	public function test_executeCore_upgrade()
	{
		$migrationClasses = [
			LocalMigrationClass1::class,
			LocalMigrationClass2::class,
			LocalMigrationClass3::class,
			LocalMigrationClass4::class,
		];
		$obj = new LocalMigrationRunnerBaseTestClass(1);

		$connection = DB::memoryKeepConnection();

		$versionSql = <<<SQL
			select
				count(*)
			from
				sqlite_master
			where
				sqlite_master.name = 'Version'
		SQL;

		$this->callInstanceMethod($obj, "executeCore", ["", $connection, $migrationClasses, LocalMigrationClassLast::class]);

		$context = $connection->open();
		$actual = $context->selectSingleCount($versionSql);
		$this->assertSame(1, $actual);
	}


	public function test_executeCore_silent()
	{
		$migrationClasses = [
			LocalMigrationClass1::class,
			LocalMigrationClass2::class,
			LocalMigrationClass3::class,
			LocalMigrationClass4::class,
		];
		$obj = new LocalMigrationRunnerBaseTestClass(4);

		$connection = DB::memoryKeepConnection();

		$versionSql = <<<SQL
			select
				count(*)
			from
				sqlite_master
			where
				sqlite_master.name = 'Version'
		SQL;

		$this->callInstanceMethod($obj, "executeCore", ["", $connection, $migrationClasses, LocalMigrationClassLast::class]);

		$context = $connection->open();
		$actual = $context->selectSingleCount($versionSql);
		$this->assertSame(0, $actual);
	}

	#endregion
}

class LocalMigrationRunnerBaseTestClass extends MigrationRunnerBase
{
	public function __construct(private int $version)
	{
		parent::__construct(LoggerFactory::createNullFactory());
	}
	protected function getCurrentVersion(string $mode, IDatabaseConnection $connection): int
	{
		return $this->version;
	}
	protected function beforeMigrate(string $mode, IDatabaseContext $context): void
	{
		//NOP
	}
	protected function afterMigrate(string $mode, IDatabaseContext $context): void
	{
		//NOP
	}
	public function execute(): void
	{
		assert(false);
	}
}


#[MigrationVersion(1)]
class LocalMigrationClass1 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("create table Version(v integer not null)");
		$argument->context->execute("insert into Version(v) values(1)");
	}
}

#[MigrationVersion(2)]
class LocalMigrationClass2 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("create table if not exists Version(v integer not null)");
		$argument->context->execute("insert into Version(v) values(2)");
	}
}

#[MigrationVersion(3)]
class LocalMigrationClass3 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("insert into Version(v) values(3)");
	}
}

#[MigrationVersion(4)]
class LocalMigrationClass4 extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("insert into Version(v) values(4)");
	}
}

class LocalMigrationClassLast extends MigrationBase
{
	use MigrationTrait;

	public function migrate(MigrationArgument $argument): void
	{
		$argument->context->execute("create table TestTable(id integer not null primary key)");
		$argument->context->execute("insert into Version(v) values(:v)", ["v" => $this->version]);
	}
}
