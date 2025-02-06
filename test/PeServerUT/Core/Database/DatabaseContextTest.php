<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use Exception;
use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseContext;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\SqlException;
use PeServer\Core\Throws\TransactionException;
use PeServerTest\TestClass;
use PeServerUT\Core\Database\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;

class DatabaseContextTest extends TestClass
{
	public function test_constructor()
	{
		DB::memory();
		$this->success();
	}

	public function test_constructor_throw()
	{
		$this->expectException(DatabaseException::class);
		new DatabaseContext(new ConnectionSetting('', '', '', null), new NullLogger());
		$this->fail();
	}

	public function test_transaction()
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

		$transactionResult1 = $database->transaction(function ($context) {
			try {
				/** @disregard P1013 */
				$context->beginTransaction();
				$this->fail();
			} catch (TransactionException) {
				$this->success();
			}
		});
		$this->assertFalse($transactionResult1);

		$transactionResult2 = $database->transaction(function ($context) {
			try {
				throw new Exception();
				$this->fail();
			} catch (Exception) {
				$this->success();
			}
		});
		$this->assertFalse($transactionResult2);
	}

	public function test_beginTransaction()
	{
		$database = DB::memory();
		$this->assertFalse($database->inTransaction());
		$database->beginTransaction();
		$this->assertTrue($database->inTransaction());
		$this->expectException(TransactionException::class);
		$database->beginTransaction();
		$this->fail();
	}

	public function test_commit()
	{
		$database = DB::memory();
		$database->beginTransaction();
		$database->commit();

		$this->expectException(TransactionException::class);
		$database->commit();
		$this->fail();
	}

	public function test_rollback()
	{
		$database = DB::memory();
		$database->beginTransaction();
		$database->rollback();

		$this->expectException(TransactionException::class);
		$database->rollback();
		$this->fail();
	}

	public static function provider_escapeLike()
	{
		return [
			["value", "value"],
			["10\\%", "10%"],
			["10\\\\", "10\\"],
			["10\\_", "10_"],
			["\\_10\\%", "_10%"],
		];
	}

	#[DataProvider('provider_escapeLike')]
	public function test_escapeLike(string $expected, string $value)
	{
		$database = DB::memory();

		$actual = $database->escapeLike($value);
		$this->assertSame($expected, $actual);
	}

	public static function provider_escapeValue()
	{
		return [
			["'value'", "value"],
			["null", null],
			["'a''a'", "a'a"],
		];
	}

	#[DataProvider('provider_escapeValue')]
	public function test_escapeValue(string $expected, mixed $value)
	{
		$database = DB::memory();

		$actual = $database->escapeValue($value);
		$this->assertSame($expected, $actual);
	}

	public function test_fetch()
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
			$this->assertSame($num, $row['id']);
			$this->assertSame("value-{$num}", $row['value']);
		}

		$this->expectException(NotSupportedException::class);
		$actual->rewind();
		$this->fail();
	}

	public function test_fetch_mapping()
	{
		$database = DB::memory();
		$result = $database->fetch(<<<SQL

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

		$actual = $result->mapping(Mapping_fetch_mapping::class);

		$this->assertSame('id', $result->columns[0]->name);
		$this->assertSame('value', $result->columns[1]->name);

		foreach ($actual as $index => $row) {
			$num = $index + 1;
			$this->assertSame($num, $row->id);
			$this->assertSame("value-{$num}", $row->value);
		}

		$this->expectException(NotSupportedException::class);
		$actual->rewind();
		$this->fail();
	}

	public function test_query()
	{
		$database = DB::memory();
		$actual = $database->query("select 'text' as COL");
		$this->assertSame(1, $actual->getRowsCount());
		foreach ($actual->rows as $row) {
			$this->assertSame(['COL' => 'text'], $row);
		}
	}

	public function test_query_mapping()
	{
		$database = DB::memory();
		$actual = $database->query("select 'text0' as text union all select 'text1' as text order by text");
		$this->assertSame(2, $actual->getRowsCount());
		foreach ($actual->mapping(Mapping_query_mapping::class) as $index => $row) {
			$this->assertSame("text{$index}", $row->text);
		}
	}

	public function test_query_parameter_throw()
	{
		$this->expectException(DatabaseException::class);
		$database = DB::memory();
		$x = $database->query('select :a as COL', ['b' => 123]);
		$this->fail();
	}

	public function test_query_sql_throw()
	{
		$this->expectException(SqlException::class);
		$database = DB::memory();
		$database->query('@@@@@');
		$this->fail();
	}

	public function test_queryFirst()
	{
		$database = DB::memory();
		$actual = $database->queryFirst("select 'text' as COL");
		$this->assertSame(['COL' => 'text'], $actual->fields);
	}

	public function test_queryFirst_throw()
	{
		$database = DB::memory();
		$this->expectException(DatabaseException::class);
		$database->queryFirst("select 'text' as COL where 1 = 0");
		$this->fail();
	}

	public function test_queryFirst_mapping_class()
	{
		$database = DB::memory();
		$result = $database->queryFirst("select 'text' as text, 123 as number");
		/** @var Mapping_queryFirst_mapping */
		$actual = $result->mapping(Mapping_queryFirst_mapping::class);
		$this->assertSame('text', $actual->text);
		$this->assertSame(123, $actual->number);
	}

	public function test_queryFirst_mapping_object()
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

	public function test_queryFirstOrNull()
	{
		$database = DB::memory();
		$actual = $database->queryFirstOrNull("select 'text' as COL");
		$this->assertSame(['COL' => 'text'], $actual->fields);
	}

	public function test_queryFirstOrNull_null()
	{
		$database = DB::memory();
		$actual = $database->queryFirstOrNull("select 'text' as COL where 1 = 0");
		$this->assertNull($actual);
	}

	public function test_querySingle()
	{
		$database = DB::memory();
		$actual = $database->querySingle(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text2'

			SQL
		);
		$this->assertSame(['COL' => 'text2'], $actual->fields);
	}

	public function test_querySingle_throw_0()
	{
		$database = DB::memory();
		$this->expectException(DatabaseException::class);
		$database->querySingle(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text0'

			SQL
		);
		$this->fail();
	}

	public function test_querySingle_throw_2()
	{
		$database = DB::memory();
		$this->expectException(DatabaseException::class);
		$database->querySingle(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text2'
				or
				COL = 'text3'

			SQL
		);
		$this->fail();
	}

	public function test_querySingleOrNull()
	{
		$database = DB::memory();
		$actual = $database->querySingleOrNull(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text2'

			SQL
		);
		$this->assertSame(['COL' => 'text2'], $actual->fields);
	}

	public function test_querySingleOrNull_0()
	{
		$database = DB::memory();
		$actual = $database->querySingleOrNull(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text4'

			SQL
		);
		$this->assertNull($actual);
	}

	public function test_querySingleOrNull_2()
	{
		$database = DB::memory();
		$actual = $database->querySingleOrNull(
			<<<SQL

			select
				*
			from
				(
					select 'text1' as COL
					union all
					select 'text2' as COL
					union all
					select 'text3' as COL
				)
			where
				COL = 'text2'
				or
				COL = 'text3'

			SQL
		);
		$this->assertNull($actual);
	}

	public function test_selectOrdered()
	{
		$database = DB::memory();
		$actual = $database->selectOrdered(
			<<<SQL

			select
				*
			from
				(
					select 0 as COL
					union all
					select 10 as COL
					union all
					select -10 as COL
				)
			order by
					COL

			SQL
		);
		$this->assertSame(-10, $actual->rows[0]['COL']);
		$this->assertSame(0, $actual->rows[1]['COL']);
		$this->assertSame(10, $actual->rows[2]['COL']);
	}

	public function test_selectOrdered_throw()
	{
		$database = DB::memory();
		$this->expectException(SqlException::class);
		$this->expectExceptionMessage('order by');
		$database->selectOrdered(
			<<<SQL

			select
				*
			from
				(
					select 0 as COL
					union all
					select 10 as COL
					union all
					select -10 as COL
				)

			SQL
		);
		$this->fail();
	}

	public function test_selectSingleCount()
	{
		$database = DB::memory();
		$actual = $database->selectSingleCount(
			<<<SQL

			select
				count(*)
			from
				(
					select 0 as COL
					union all
					select 10 as COL
					union all
					select -10 as COL
				)
			order by
					COL

			SQL
		);
		$this->assertSame(3, $actual);
	}

	public function test_selectSingleCount_name()
	{
		$database = DB::memory();
		$actual = $database->selectSingleCount(
			<<<SQL

			select
				count(*) as COL_NAME
			from
				(
					select 0 as COL
					union all
					select 10 as COL
					union all
					select -10 as COL
				)
			order by
					COL

			SQL
		);
		$this->assertSame(3, $actual);
	}

	public function test_selectSingleCount_throw_sql()
	{
		$database = DB::memory();
		$this->expectException(DatabaseException::class);
		$database->selectSingleCount(
			<<<SQL

			select
				'text' as COL2,
				count(*) as COL_NAME
			from
				(
					select 0 as COL
					union all
					select 10 as COL
					union all
					select -10 as COL
				)
			order by
					COL

			SQL
		);
		$this->fail();
	}

	public function test_selectSingleCount_throw_count()
	{
		$database = DB::memory();
		$this->expectException(SqlException::class);
		$database->selectSingleCount("select 'text' as COL");
		$this->fail();
	}

	public function test_execute()
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

	public function test_insert()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer)');
		$actual = $database->insert('insert into TBL(COL) values (10)');
		$this->assertSame(1, $actual);
	}

	public function test_insert_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer)');
		$this->expectException(SqlException::class);
		$database->insert('select * from TBL');
		$this->fail();
	}

	public function test_insertSingle()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer)');
		$database->insertSingle('insert into TBL(COL) values (10)');
		$this->success();
	}

	public function test_insertSingle_throw_sql()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer)');
		$this->expectException(SqlException::class);
		$database->insertSingle('select * from TBL');
		$this->fail();
	}

	public function test_insertSingle_throw_2()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer)');
		$this->expectException(DatabaseException::class);
		$database->insertSingle('insert into TBL(COL) values (10), (20)');
		$this->fail();
	}

	public function test_update()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$database->update('update TBL set VAL = VAL || VAL');
		$this->assertSame('AA', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertSame('BB', $database->querySingle('select VAL from TBL where COL = 20')->fields['VAL']);
	}

	public function test_update_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->expectException(SqlException::class);
		$database->update('select * from TBL');
		$this->fail();
	}

	public function test_updateByKey()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$database->updateByKey('update TBL set VAL = VAL || VAL where COL = 20');
		$this->assertSame('A', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertSame('BB', $database->querySingle('select VAL from TBL where COL = 20')->fields['VAL']);
	}

	public function test_updateByKey_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->expectException(DatabaseException::class);
		$database->updateByKey('update TBL set VAL = VAL || VAL');
		$this->fail();
	}

	public function test_updateByKeyOrNothing()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");

		$actual = $database->updateByKeyOrNothing('update TBL set VAL = VAL || VAL where COL = 20');
		$this->assertTrue($actual);
		$this->assertSame('A', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertSame('BB', $database->querySingle('select VAL from TBL where COL = 20')->fields['VAL']);
	}

	public function test_updateByKeyOrNothing_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");

		$actual = $database->updateByKeyOrNothing('update TBL set VAL = VAL || VAL where COL = 30');
		$this->assertFalse($actual);
		$this->assertSame('A', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertSame('B', $database->querySingle('select VAL from TBL where COL = 20')->fields['VAL']);
	}

	public function test_delete()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$database->delete('delete from TBL');
		$this->assertSame(0, $database->selectSingleCount('select count(*) from TBL'));
	}

	public function test_delete_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->expectException(SqlException::class);
		$database->delete('select * from TBL');
		$this->fail();
	}

	public function test_deleteByKey()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$database->deleteByKey('delete from TBL where COL = 20');
		$this->assertSame('A', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertNull($database->querySingleOrNull('select VAL from TBL where COL = 20'));
	}

	public function test_deleteByKey_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->expectException(DatabaseException::class);
		$database->deleteByKey('delete from TBL where COL = 30');
		$this->fail();
	}

	public function test_deleteByKeyOrNothing()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->assertTrue($database->deleteByKeyOrNothing('delete from TBL where COL = 20'));
		$this->assertSame('A', $database->querySingle('select VAL from TBL where COL = 10')->fields['VAL']);
		$this->assertNull($database->querySingleOrNull('select VAL from TBL where COL = 20'));
	}


	public function test_deleteByKeyOrNothing_throw()
	{
		$database = DB::memory();
		$database->execute('create table TBL(COL integer, VAL text)');
		$database->execute("insert into TBL(COL, VAL) values (10, 'A'), (20, 'B')");
		$this->expectException(DatabaseException::class);
		$database->deleteByKeyOrNothing('delete from TBL');
		$this->fail();
	}
}

class Mapping_fetch_mapping
{
	#region variable

	public int $id;
	public string $value;

	#endregion
}

class Mapping_query_mapping
{
	#region variable

	public string $text;

	#endregion
}

class Mapping_queryFirst_mapping
{
	#region variable

	public string $text;
	public int $number;

	#endregion
}
