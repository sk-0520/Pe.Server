<?php

declare(strict_types=1);

namespace PeServerTest\Core\Database;

use PeServer\Core\Archiver;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Binary;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\SqlException;
use PeServerTest\Core\Database\DB;
use PeServerTest\TestClass;
use stdClass;

class DatabaseContextTest extends TestClass
{
	function test_constructor()
	{
		DB::memory();
		$this->success();
	}

	function test_constructor_throw()
	{
		$this->expectException(DatabaseException::class);
		new DatabaseContext(new ConnectionSetting('', '', '', null), new NullLogger());
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

	function test_execute()
	{
		$database = DB::memory();
		$actual1 = $database->execute('PRAGMA database_list');
		$this->assertSame(0, $actual1->resultCount);

		$actual2 = $database->execute('create table test(field1 integer)');
		$this->assertSame(0, $actual2->resultCount);

		$actual3 = $database->execute('insert into test(field1) values (1)');
		$this->assertSame(1, $actual3->resultCount);

		$actual4 = $database->execute('insert into test(field1) values (2)');
		$this->assertSame(1, $actual4->resultCount);

		$actual5 = $database->execute('insert into test(field1) values (3)');
		$this->assertSame(1, $actual5->resultCount);

		$actual6 = $database->execute('update test set field1 = field1 * 10 where field1 <> 1');
		$this->assertSame(2, $actual6->resultCount);

		$actual7 = $database->execute('delete from test');
		$this->assertSame(3, $actual7->resultCount);
	}
}
