<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServerTest\TestClass;

class DatabaseManagementTest extends TestClass
{
	#region function

	public function test_getDatabaseItems()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$actual = $management->getDatabaseItems();

		$this->assertCount(1, $actual);
		$this->assertSame("main", $actual[0]->name);
	}

	public function test_getDatabaseItems_temp()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$database->execute("create temporary table zzzz(id integer)");

		$actual = $management->getDatabaseItems();

		$this->assertCount(2, $actual);

		$this->assertSame("main", $actual[0]->name);
		$this->assertSame("temp", $actual[1]->name);
	}

	public function test_getSchemaItems()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$actual = $management->getSchemaItems($db[0]);

		$this->assertCount(1, $actual);
		$this->assertSame("main", $actual[0]->name);
	}

	public function test_getSchemaItems_temp()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$database->execute("create temporary table zzzz(id integer)");

		$db = $management->getDatabaseItems();
		$actual_main = $management->getSchemaItems($db[0]);
		$actual_temp = $management->getSchemaItems($db[1]);

		$this->assertCount(1, $actual_main);
		$this->assertSame("main", $actual_main[0]->name);
		$this->assertTrue($actual_main[0]->isDefault);

		$this->assertCount(1, $actual_temp);
		$this->assertSame("temp", $actual_temp[0]->name);
		$this->assertFalse($actual_temp[0]->isDefault);
	}

	#endregion
}
