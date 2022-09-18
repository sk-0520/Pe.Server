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
use PeServer\Core\Throws\NotSupportedException;
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

	function test_fetch()
	{
		$database = DB::memory();
		$actual = $database->fetch(<<<SQL

			with dummy(id, value) as (
				values
					(1, 'value-1'),
					(2, 'value-2'),
					(3, 'value-3')
			)
			select
				dummy.id,
				dummy.value
			from
				dummy
			order by
				dummy.id

		SQL);

		$this->assertSame('id', $actual->columns[0]->name);
		$this->assertSame('value', $actual->columns[1]->name);

		foreach ($actual as $index => $row) {
			$num = $index + 1;
			$this->assertSame("{$num}", $row['id']);
			$this->assertSame("value-{$num}", $row['value']);
		}

		$this->expectException(NotSupportedException::class);
		$actual->rewind();
		$this->fail();
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

	function test_queryFirst_mapping_class()
	{
		$database = DB::memory();
		$result = $database->queryFirst("select 'text' as text, 123 as number");
		/** @var Mapping_queryFirst_mapping */
		$actual = $result->mapping(Mapping_queryFirst_mapping::class);
		$this->assertSame('text', $actual->text);
		$this->assertSame(123, $actual->number);
	}

	function test_queryFirst_mapping_object()
	{
		$database = DB::memory();
		$result = $database->queryFirst("select 'text' as text, 123 as number");
		$object = new Mapping_queryFirst_mapping();
		/** @var Mapping_queryFirst_mapping */
		$actual = $result->mapping($object);
		$this->assertSame($object, $actual);
		$this->assertSame('text', $actual->text);
		$this->assertSame(123, $actual->number);
	}

	function test_execute()
	{
		$database = DB::memory();
		$actual1 = $database->execute('PRAGMA database_list');
		$this->assertSame(0, $actual1->getResultCount());

		$actual2 = $database->execute('create table test(field1 integer)');
		$this->assertSame(0, $actual2->getResultCount());

		$actual3 = $database->execute('insert into test(field1) values (1)');
		$this->assertSame(1, $actual3->getResultCount());

		$actual4 = $database->execute('insert into test(field1) values (2)');
		$this->assertSame(1, $actual4->getResultCount());

		$actual5 = $database->execute('insert into test(field1) values (3)');
		$this->assertSame(1, $actual5->getResultCount());

		$actual6 = $database->execute('update test set field1 = field1 * 10 where field1 <> 1');
		$this->assertSame(2, $actual6->getResultCount());

		$actual7 = $database->execute('delete from test');
		$this->assertSame(3, $actual7->getResultCount());
	}
}

class Mapping_queryFirst_mapping
{
	#region variable

	public string $text;
	public int $number;

	#endregion
}
