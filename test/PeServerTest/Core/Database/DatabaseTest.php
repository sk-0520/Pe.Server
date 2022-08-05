<?php

declare(strict_types=1);

namespace PeServerTest\Core\Database;

use PeServer\Core\Archiver;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Binary;
use PeServer\Core\Database\Database;
use PeServer\Core\Log\Logging;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\SqlException;
use PeServerTest\Core\Database\DB;
use PeServerTest\TestClass;
use stdClass;

class DatabaseTest extends TestClass
{
	function test_constructor()
	{
		DB::memory();
		$this->success();
	}

	function test_constructor_throw()
	{
		$this->expectException(DatabaseException::class);
		new Database('', '', '', null, Logging::create(get_class($this)));
		$this->fail();
	}

	function test_transaction()
	{
		$database = DB::memory();
		$this->assertFalse($database->inTransaction());

		$database->beginTransaction();
		$this->assertTrue($database->inTransaction());
		$database->commit();
		$this->assertFalse($database->inTransaction());

		$database->beginTransaction();
		$this->assertTrue($database->inTransaction());
		$database->rollback();
		$this->assertFalse($database->inTransaction());
	}

	function test_query()
	{
		$database = DB::memory();
		$actual = $database->query("select 'text' as COL");
		$this->assertSame(1, $actual->getRowsCount());
		foreach ($actual->rows as $row) {
			$this->assertSame(['COL' => 'text'], $row);
		}
	}

	function test_query_parameter_throw()
	{
		$this->expectException(DatabaseException::class);
		$database = DB::memory();
		$x = $database->query('select :a as COL', ['b' => 123]);
		$this->fail();
	}

	function test_query_sql_throw()
	{
		$this->expectException(SqlException::class);
		$database = DB::memory();
		$database->query('@@@@@');
		$this->fail();
	}

	function test_queryFirst()
	{
		$database = DB::memory();
		$actual = $database->queryFirst("select 'text' as COL");
		$this->assertSame(['COL' => 'text'], $actual->fields);
	}
}
