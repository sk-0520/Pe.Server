<?php

declare(strict_types=1);

namespace PeServerUT\Core\Migration;

use PeServer\Core\Migration\MigrationVersion;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use stdClass;

class MigrationVersionTest extends TestClass
{
	#region function

	public function test_getVersion_throw_no_attr()
	{
		$this->expectException(ArgumentException::class);

		MigrationVersion::getVersion(new stdClass());
	}

	public function test_getVersion_obj()
	{
		$actual1 = MigrationVersion::getVersion(new LocalMigrationVersionTestClass1());
		$this->assertSame(1, $actual1);

		$actual2 = MigrationVersion::getVersion(new LocalMigrationVersionTestClass2());
		$this->assertSame(2, $actual2);
	}

	public function test_getVersion_class()
	{
		$actual1 = MigrationVersion::getVersion(LocalMigrationVersionTestClass1::class);
		$this->assertSame(1, $actual1);

		$actual2 = MigrationVersion::getVersion(LocalMigrationVersionTestClass2::class);
		$this->assertSame(2, $actual2);
	}

	#endregion
}

#[MigrationVersion(1)]
class LocalMigrationVersionTestClass1
{
	//NOP
}

#[MigrationVersion(2)]
class LocalMigrationVersionTestClass2
{
	//NOP
}
