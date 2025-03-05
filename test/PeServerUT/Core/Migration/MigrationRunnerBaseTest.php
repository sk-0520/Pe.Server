<?php

declare(strict_types=1);

namespace PeServerUT\Core\Migration;

use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Migration\MigrationRunnerBase;
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
					LocalMigrationRunnerBaseTestClass1::class,
					LocalMigrationRunnerBaseTestClass1::class,
				]
			],
			[
				"index: 0",
				[
					LocalMigrationRunnerBaseTestClass2::class,
					LocalMigrationRunnerBaseTestClass1::class,
				]
			],
			[
				"index: 1",
				[
					LocalMigrationRunnerBaseTestClass1::class,
					LocalMigrationRunnerBaseTestClass3::class,
					LocalMigrationRunnerBaseTestClass2::class,
				]
			],
		];
	}

	#[DataProvider('provider_executeCore_throw_sorted')]
	public function test_executeCore_throw_sorted(string $expected, array $migrationClasses)
	{
		$obj = new class extends MigrationRunnerBase
		{
			public function __construct()
			{
				parent::__construct(LoggerFactory::createNullFactory());
			}
			protected function getCurrentVersion(string $mode, IDatabaseConnection $connection): int
			{
				assert(false);
			}
			protected function beforeMigrate(string $mode, IDatabaseContext $context): void
			{
				assert(false);
			}
			protected function afterMigrate(string $mode, IDatabaseContext $context): void
			{
				assert(false);
			}
			public function execute(): void
			{
				assert(false);
			}
		};
		$connection = DB::memoryConnection();

		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage($expected);

		$this->callInstanceMethod($obj, "executeCore", ["", $connection, $migrationClasses, LocalMigrationRunnerBaseTestClassLast::class]);
	}

	#endregion
}

class LocalMigrationRunnerBaseTestClassLast {}

#[MigrationVersion(1)]
class LocalMigrationRunnerBaseTestClass1
{
	//NOP
}

#[MigrationVersion(2)]
class LocalMigrationRunnerBaseTestClass2
{
	//NOP
}

#[MigrationVersion(3)]
class LocalMigrationRunnerBaseTestClass3
{
	//NOP
}

#[MigrationVersion(4)]
class LocalMigrationRunnerBaseTestClass4
{
	//NOP
}
