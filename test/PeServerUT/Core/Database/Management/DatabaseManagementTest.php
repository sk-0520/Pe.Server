<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServer\Core\Collections\Collection;
use PeServer\Core\Database\Management\DatabaseResourceItem;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\TypeUtility;
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

	public function test_getResourceItems_none()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = $management->GetResourceItems($schemaItem, 0);

		$this->assertEmpty($resourceItems);
	}

	public function test_getResourceItems_empty()
	{
		$database = DB::memory();
		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = $management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_ALL);

		$this->assertEmpty($resourceItems);
	}

	public function test_getResourceItems_singleTable()
	{
		$database = DB::memory();
		$sql = "create table T(value text)";
		$database->execute($sql);
		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = $management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_ALL);

		$this->assertCount(1, $resourceItems);

		$actual = $resourceItems[0];
		$this->assertSame("T", $actual->name);
		$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual->kind);
		$this->assertSame(Text::toLower($sql), Text::toLower($actual->source));
	}

	public function test_getResourceItems_analyze()
	{
		$database = DB::memory();
		$database->execute("create table T(value text)");
		$database->execute("analyze\n");

		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = Collection::from($management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_ALL));

		$this->assertLessThanOrEqual(2, $resourceItems->count());

		$actual_1 = $resourceItems->first(fn($a) => $a->name == "T");
		$this->assertSame("T", $actual_1->name);
		$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual_1->kind);

		$actual_2 = $resourceItems->first(fn($a) => $a->name == "sqlite_stat1");
		$this->assertSame("sqlite_stat1", $actual_2->name);
		$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual_2->kind);

		$actual_3 = $resourceItems->firstOr(null, fn($a) => $a->name == "sqlite_stat4");
		if ($actual_3) {
			$this->assertSame("sqlite_stat4", $actual_3->name);
			$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual_3->kind);
		}
	}

	public function test_getResourceItems_view()
	{
		$database = DB::memory();
		$database->execute("create table T(value text)");
		$database->execute("create view V as select * from T");

		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = Collection::from($management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_ALL));

		$this->assertCount(2, $resourceItems);

		$actual_1 = $resourceItems->first(fn($a) => $a->name == "T");
		$this->assertSame("T", $actual_1->name);
		$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual_1->kind);

		$actual_2 = $resourceItems->first(fn($a) => $a->name == "V");
		$this->assertSame("V", $actual_2->name);
		$this->assertSame(DatabaseResourceItem::KIND_VIEW, $actual_2->kind);
	}

	public function test_getResourceItems_index()
	{
		$database = DB::memory();
		$database->execute("create table T(value text)");
		$database->execute("create index I on T(value)");

		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItems = Collection::from($management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_ALL));

		$this->assertCount(2, $resourceItems);

		$actual_1 = $resourceItems->first(fn($a) => $a->name == "T");
		$this->assertSame("T", $actual_1->name);
		$this->assertSame(DatabaseResourceItem::KIND_TABLE, $actual_1->kind);

		$actual_2 = $resourceItems->first(fn($a) => $a->name == "I");
		$this->assertSame("I", $actual_2->name);
		$this->assertSame(DatabaseResourceItem::KIND_INDEX, $actual_2->kind);
	}

	public function test_getColumns_throw()
	{
		$database = DB::memory();
		$database->execute("create table T(t text primary key, i integer, r real, b blob, nn integer not null, dv text default 'TEXT', n numeric(10,3))");

		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItem = $management->GetResourceItems($schemaItem, DatabaseResourceItem::KIND_TABLE)[0];

		$invalidResource = new DatabaseResourceItem(
			$resourceItem->schema,
			$resourceItem->name,
			DatabaseResourceItem::KIND_INDEX,
			$resourceItem->source
		);

		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage("not table");
		$management->getColumns($invalidResource);
	}

	public function test_getColumns()
	{
		$database = DB::memory();
		$database->execute("create table T(t text primary key, i integer, r real, b blob, nn integer not null, dv text default 'TEXT', n numeric(10,3))");

		$management = $database->getManagement();

		$db = $management->getDatabaseItems();
		$schemaItem = $management->getSchemaItems($db[0])[0];

		$resourceItem = $management->getResourceItems($schemaItem, DatabaseResourceItem::KIND_TABLE)[0];

		$actual = $management->getColumns($resourceItem);
		$this->assertCount(7, $actual);

		{
			$actualTarget = $actual[0];
			$this->assertSame("t", $actualTarget->name);
			$this->assertTrue($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_STRING, $actualTarget->type->phpType);
			$this->assertSame("TEXT", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[1];
			$this->assertSame("i", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_INTEGER, $actualTarget->type->phpType);
			$this->assertSame("INTEGER", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[2];
			$this->assertSame("r", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_DOUBLE, $actualTarget->type->phpType);
			$this->assertSame("REAL", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[3];
			$this->assertSame("b", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_STRING, $actualTarget->type->phpType);
			$this->assertSame("BLOB", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[4];
			$this->assertSame("nn", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertFalse($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_INTEGER, $actualTarget->type->phpType);
			$this->assertSame("INTEGER", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[5];
			$this->assertSame("dv", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertSame("'TEXT'", $actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_STRING, $actualTarget->type->phpType);
			$this->assertSame("TEXT", $actualTarget->type->rawType);
		}
		{
			$actualTarget = $actual[6];
			$this->assertSame("n", $actualTarget->name);
			$this->assertFalse($actualTarget->isPrimary);
			$this->assertTrue($actualTarget->isNullable);
			$this->assertEmpty($actualTarget->defaultValue);
			$this->assertSame(TypeUtility::TYPE_INTEGER, $actualTarget->type->phpType);
			$this->assertSame("NUMERIC(10,3)", $actualTarget->type->rawType);
		}
	}


	#endregion
}
