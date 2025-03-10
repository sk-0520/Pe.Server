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

	#endregion
}
